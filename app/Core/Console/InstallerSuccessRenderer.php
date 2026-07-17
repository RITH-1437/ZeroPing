<?php

namespace App\Core\Console;

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

    private const DESCRIPTIONS = [
        'empty'     => 'A lightweight starter containing only the essentials.',
        'mvc'       => 'A full MVC application with authentication, routing, migrations and CRUD.',
        'blog'      => 'A blog starter with posts, categories and pagination.',
        'api'       => 'RESTful API starter with authentication and JSON responses.',
        'dashboard' => 'Admin dashboard with stats, tables and user management.',
    ];

    private const LINKS = [
        'Documentation' => 'https://github.com/RITH-1437/ZeroPing/tree/main/docs',
        'GitHub'        => 'https://github.com/RITH-1437/ZeroPing',
        'Issues'        => 'https://github.com/RITH-1437/ZeroPing/issues',
    ];

    private const TIPS = [
        'SQLite is configured by default.',
        'Change DB_CONNECTION in .env to mysql, pgsql or sqlsrv.',
        'Run "php zero doctor" anytime to verify your environment.',
    ];

    private const NEXT_STEPS = [
        'cd {project}',
        'composer install',
        'php zero serve',
        'php zero migrate',
        'php zero doctor',
        'php zero test',
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
        $parts = [
            $this->renderLogo(),
            '',
            $this->line(''),
            $this->renderProjectInfo(),
            $this->line(''),
            $this->renderDescription(),
            $this->line(''),
            $this->renderNextSteps(),
            $this->line(''),
            $this->renderLinks(),
            $this->line(''),
            $this->renderTips(),
            $this->line(''),
            $this->renderFinalMessage(),
            '',
        ];

        return implode("\n", $parts);
    }

    private function renderLogo(): string
    {
        if (!$this->ansi) {
            return '';
        }

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

        $width = $this::detectWidth();
        $pad = $bannerWidth >= $width ? 0 : (int) floor(($width - $bannerWidth) / 2);
        $padStr = str_repeat(' ', $pad);

        $out = [];
        foreach ($glyphRows as $i => $line) {
            $color = self::GRADIENT[$i] ?? self::RESET;
            $out[] = $color . $padStr . $line . self::RESET;
        }

        return implode("\n", $out);
    }

    private function renderProjectInfo(): string
    {
        $db = $this->resolveDatabaseDriver();
        $lines = [
            $this->bold('Project Created Successfully!'),
            '',
            $this->label('Project Name', $this->projectName),
            $this->label('Starter Type', ucfirst($this->starterType)),
            $this->label('Framework', 'ZeroPing v' . $this->frameworkVersion),
            $this->label('PHP Version', $this->phpVersion),
            $this->label('Database', $db),
            $this->label('Location', $this->projectPath),
        ];

        return implode("\n", $lines);
    }

    private function renderDescription(): string
    {
        $desc = self::DESCRIPTIONS[$this->starterType] ?? self::DESCRIPTIONS['empty'];
        return $this->dim($desc);
    }

    private function renderNextSteps(): string
    {
        $lines = [$this->bold('Next Steps'), ''];

        $dirName = $this->slugify($this->projectName);
        foreach (self::NEXT_STEPS as $step) {
            $cmd = str_replace('{project}', $dirName, $step);
            if ($this->ansi) {
                $lines[] = '  ' . "\e[32m$\e[0m" . ' ' . "\e[97m" . $cmd . "\e[0m";
            } else {
                $lines[] = '  $ ' . $cmd;
            }
        }

        return implode("\n", $lines);
    }

    private function renderLinks(): string
    {
        $lines = [$this->bold('Useful Links'), ''];

        foreach (self::LINKS as $label => $url) {
            if ($this->ansi) {
                $lines[] = '  ' . "\e[36m" . $label . "\e[0m";
                $lines[] = '  ' . "\e[90m" . $url . "\e[0m";
            } else {
                $lines[] = '  ' . $label;
                $lines[] = '  ' . $url;
            }
            $lines[] = '';
        }

        return rtrim(implode("\n", $lines));
    }

    private function renderTips(): string
    {
        $lines = [$this->bold('Tips'), ''];

        foreach (self::TIPS as $tip) {
            if ($this->ansi) {
                $lines[] = '  ' . "\e[32m\u{2713}\e[0m" . ' ' . $tip;
            } else {
                $lines[] = '  * ' . $tip;
            }
        }

        return implode("\n", $lines);
    }

    private function renderFinalMessage(): string
    {
        if ($this->ansi) {
            return implode("\n", [
                $this->line(''),
                "\e[1;33mHappy coding with ZeroPing!\e[0m",
                '',
                "\e[90mBuild fast.\e[0m",
                "\e[90mBuild clean.\e[0m",
                "\e[90mBuild confidently.\e[0m",
            ]);
        }

        return implode("\n", [
            '',
            'Happy coding with ZeroPing!',
            '',
            'Build fast.',
            'Build clean.',
            'Build confidently.',
        ]);
    }

    private function label(string $key, string $value): string
    {
        if ($this->ansi) {
            return '  ' . "\e[90m" . $key . "\e[0m" . ' : ' . "\e[97m" . $value . "\e[0m";
        }
        return '  ' . $key . ' : ' . $value;
    }

    private function bold(string $text): string
    {
        if ($this->ansi) {
            return "\e[1;97m" . $text . "\e[0m";
        }
        return $text;
    }

    private function dim(string $text): string
    {
        if ($this->ansi) {
            return "\e[2m" . $text . "\e[0m";
        }
        return $text;
    }

    private function line(string $text): string
    {
        $width = min(self::detectWidth(), 80);
        return str_repeat("\e[90m\u{2500}\e[0m", $width);
    }

    private function slugify(string $name): string
    {
        return strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', $name), '-'));
    }

    private function resolveDatabaseDriver(): string
    {
        if (isset($_ENV['DB_CONNECTION'])) {
            return strtoupper($_ENV['DB_CONNECTION']);
        }
        $envFile = rtrim($this->projectPath, '\\/') . '/.env';
        if (file_exists($envFile)) {
            $contents = file_get_contents($envFile);
            if (preg_match('/^DB_CONNECTION\s*=\s*(\w+)/m', $contents, $m)) {
                return strtoupper($m[1]);
            }
        }
        return 'SQLITE';
    }

    private static function detectWidth(): int
    {
        $columns = (int) ($_ENV['COLUMNS'] ?? 0);
        if ($columns > 0) {
            return min(max($columns, 60), 120);
        }

        if (PHP_OS_FAMILY === 'Windows') {
            $output = @shell_exec('mode con 2>NUL');
            if ($output && preg_match('/Columns:\s*(\d+)/i', $output, $m)) {
                return min(max((int) $m[1], 60), 120);
            }
        } else {
            $output = @shell_exec('tput cols 2>/dev/null');
            if ($output && is_numeric(trim($output))) {
                return min(max((int) trim($output), 60), 120);
            }
        }

        return 80;
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
