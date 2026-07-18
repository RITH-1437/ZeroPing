<?php

namespace App\Core\Console;

/**
 * Renders the post-install success summary for a freshly generated
 * ZeroPing application. Output is a clean, boxed summary that works both in
 * ANSI terminals (with a faint logo + colours) and in plain text.
 */
class InstallerSuccessRenderer
{
    private const WORD = 'ZEROPING';

    private const GLYPH_GAP = ' ';

    private const GLYPHS = [
        'Z' => ['#######', '   ####', '  #### ', ' ####  ', '####   ', '#######'],
        'E' => ['#######', '##     ', '##     ', '#######', '##     ', '#######'],
        'R' => ['#######', '##   ##', '#######', '## ##  ', '##  ## ', '##   ##'],
        'O' => ['#######', '##   ##', '##   ##', '##   ##', '##   ##', '#######'],
        'P' => ['#######', '##   ##', '#######', '##     ', '##     ', '##     '],
        'I' => ['#######', '  ###  ', '  ###  ', '  ###  ', '  ###  ', '#######'],
        'N' => ['##   ##', '###  ##', '## # ##', '##  ###', '##   ##', '##   ##'],
        'G' => ['#######', '##     ', '##     ', '##  ###', '##   ##', '#######'],
    ];

    private const GRADIENT = ["\e[36m", "\e[36m", "\e[34m", "\e[34m", "\e[32m", "\e[32m"];

    private const BLOCK = '█';

    private const RESET = "\e[0m";

    private const DOCS_URL = 'https://zero-ping.duckdns.org/docs/introduction';

    private const APP_URL = 'http://127.0.0.1:8000';

    private const NEXT_STEPS = [
        'cd {project}',
        'composer install',
        'php zero install',
        'php zero migrate',
        'php zero serve',
    ];

    private bool $ansi;

    public function __construct(
        private string $projectName,
        private string $starterType,
        private string $frameworkVersion,
        private string $phpVersion,
        private string $projectPath,
    ) {
        $this->ansi = $this->detectAnsi();
    }

    public function render(): string
    {
        $parts = [];

        if ($this->ansi) {
            $parts[] = $this->renderLogo();
            $parts[] = '';
        }

        $parts[] = $this->rule();
        $parts[] = '';
        $parts[] = $this->centerIfAnsi('Welcome to ZeroPing');
        $parts[] = '';
        $parts[] = $this->section('Project', $this->slugify($this->projectName));
        $parts[] = '';
        $parts[] = $this->section('Next steps', null, $this->nextSteps());
        $parts[] = '';
        $parts[] = $this->section('Application', self::APP_URL);
        $parts[] = '';
        $parts[] = $this->section('Documentation', self::DOCS_URL);
        $parts[] = '';
        $parts[] = $this->rule();
        $parts[] = '';

        return implode("\n", $parts);
    }

    private function renderLogo(): string
    {
        $letters = str_split(self::WORD);
        $rows = count(self::GLYPHS['Z']);
        $glyphRows = [];

        for ($r = 0; $r < $rows; $r++) {
            $line = '';
            foreach ($letters as $i => $ch) {
                $line .= self::toBlock(self::GLYPHS[$ch][$r]);
                if ($i < count($letters) - 1) {
                    $line .= self::GLYPH_GAP;
                }
            }
            $glyphRows[] = $line;
        }

        $bannerWidth = 0;
        foreach ($glyphRows as $line) {
            $len = mb_strlen(preg_replace('/\e\[[0-9;]*m/', '', $line));
            $bannerWidth = max($bannerWidth, $len);
        }

        $width = self::detectWidth();
        $pad = $bannerWidth >= $width ? 0 : (int) floor(($width - $bannerWidth) / 2);
        $padStr = str_repeat(' ', $pad);

        $out = [];
        foreach ($glyphRows as $i => $line) {
            $color = self::GRADIENT[$i] ?? self::RESET;
            $out[] = $color . $padStr . $line . self::RESET;
        }

        return implode("\n", $out);
    }

    private function section(string $title, ?string $value, ?array $lines = null): string
    {
        if ($this->ansi) {
            $titleLine = "\e[1;97m{$title}:\e[0m";
        } else {
            $titleLine = "{$title}:";
        }

        if ($lines !== null) {
            $body = array_map(
                fn (string $l) => $this->ansi ? '  ' . "\e[36m➜\e[0m " . "\e[97m" . $l . "\e[0m" : '  ' . $l,
                $lines
            );
            return $titleLine . "\n" . implode("\n", $body);
        }

        $rendered = $this->ansi ? '  ' . "\e[32m" . $value . "\e[0m" : '  ' . $value;

        return $titleLine . "\n" . $rendered;
    }

    private function nextSteps(): array
    {
        $dirName = $this->slugify($this->projectName);
        return array_map(
            fn (string $step) => str_replace('{project}', $dirName, $step),
            self::NEXT_STEPS
        );
    }

    private function centerIfAnsi(string $text): string
    {
        if (!$this->ansi) {
            return $text;
        }
        return "\e[1;36m{$text}\e[0m";
    }

    private function rule(): string
    {
        $width = min(self::detectWidth(), 60);
        $bar = str_repeat("\u{2501}", $width);
        return $this->ansi ? "\e[90m{$bar}\e[0m" : $bar;
    }

    private function slugify(string $name): string
    {
        return strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', $name), '-'));
    }

    private static function detectWidth(): int
    {
        $columns = (int) ($_ENV['COLUMNS'] ?? 0);
        if ($columns > 0) {
            return min(max($columns, 40), 120);
        }

        if (PHP_OS_FAMILY === 'Windows') {
            $output = @shell_exec('mode con 2>NUL');
            if ($output && preg_match('/Columns:\s*(\d+)/i', $output, $m)) {
                return min(max((int) $m[1], 40), 120);
            }
        } else {
            $output = @shell_exec('tput cols 2>/dev/null');
            if ($output && is_numeric(trim($output))) {
                return min(max((int) trim($output), 40), 120);
            }
        }

        return 60;
    }

    private static function toBlock(string $line): string
    {
        return str_replace('#', self::BLOCK, $line);
    }

    private function detectAnsi(): bool
    {
        if (getenv('NO_COLOR') !== false && getenv('NO_COLOR') !== '') {
            return false;
        }

        if (getenv('TERM') === 'dumb') {
            return false;
        }

        if (PHP_OS_FAMILY === 'Windows') {
            return getenv('ANSICON') !== false
                || str_contains(getenv('TERM_PROGRAM') ?: '', 'vscode')
                || (getenv('TERM') !== false && str_contains(getenv('TERM'), 'xterm'))
                || (getenv('ConEmuANSI') === 'ON');
        }

        return true;
    }
}
