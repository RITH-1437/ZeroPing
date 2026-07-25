<?php

namespace App\Core\Console;

abstract class Command
{
    protected array $options = [];

    protected string $description = '';

    protected ConsoleStyle $output;

    public function __construct()
    {
        $this->output = new ConsoleStyle();

        $this->parseOptions();
    }

    /**
     * Render a stub template from the Stubs directory.
     */
    protected function stub(string $name): string
    {
        $path = __DIR__ . "/Stubs/{$name}";

        if (!file_exists($path)) {
            throw new \RuntimeException("Stub {$name} not found.");
        }

        return file_get_contents($path);
    }

    /**
     * Replace {{ key }} placeholders in a stub.
     */
    protected function replace(
        string $stub,
        array $replace
    ): string {

        foreach ($replace as $search => $value) {
            $stub = str_replace(
                '{{ ' . $search . ' }}',
                $value,
                $stub
            );
        }

        return $stub;
    }

    /**
     * Write content to a file, creating directories as needed.
     */
    protected function write(
        string $file,
        string $content
    ): void {

        $directory = dirname($file);

        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        file_put_contents(
            $file,
            $content
        );
    }

    // ── Output helpers ───────────────────────────────────────────────────────

    protected function line(string $message = ''): void
    {
        $this->output->writeln($message);
    }

    protected function info(string $message): void
    {
        $this->output->writeln("<fg=green>{$message}</>");
    }

    protected function success(string $message): void
    {
        $this->output->writeln("<fg=green>✅ {$message}</>");
    }

    protected function error(string $message): void
    {
        $this->output->writeln("<fg=red>❌ {$message}</>");
    }

    protected function warn(string $message): void
    {
        $this->output->writeln("<fg=yellow>⚠️  {$message}</>");
    }

    protected function comment(string $message): void
    {
        $this->output->writeln("<fg=cyan>{$message}</>");
    }

    /**
     * Print a bold section title.
     */
    protected function title(string $text): void
    {
        $this->output->writeln('');
        $this->output->writeln("<options=bold;fg=cyan>{$text}</>");
        $this->output->writeln('<fg=gray>' . str_repeat('═', mb_strlen($text)) . '</>');
    }

    /**
     * Print a smaller sub-heading.
     */
    protected function section(string $text): void
    {
        $this->output->writeln('');
        $this->output->writeln("<fg=yellow>{$text}</>");
    }

    /**
     * Render an aligned, colorized table.
     *
     * @param string[] $headers
     * @param array<int,string[]> $rows
     */
    protected function table(array $headers, array $rows): void
    {
        $widths = [];

        foreach ($headers as $i => $header) {
            $widths[$i] = mb_strlen((string) $header);
        }

        foreach ($rows as $row) {
            foreach ($row as $i => $cell) {
                $widths[$i] = max($widths[$i] ?? 0, mb_strlen((string) $cell));
            }
        }

        $border = function (array $cells = []) use ($widths): string {
            $out = '  +';
            if ($cells === []) {
                foreach ($widths as $w) {
                    $out .= str_repeat('-', $w + 2) . '+';
                }
            } else {
                foreach ($cells as $i => $cell) {
                    $out .= str_repeat('-', ($widths[$i] ?? 0) + 2) . '+';
                }
            }
            return "<fg=gray>{$out}</>";
        };

        $rowLine = function (array $cells, string $color) use ($widths): string {
            $out = '  |';
            foreach ($cells as $i => $cell) {
                $raw = (string) $cell;
                $visible = preg_replace('/<[^>]+>/', '', $raw);
                $target = ($widths[$i] ?? 0) + 1;
                $padding = $target - mb_strlen($visible);
                if ($padding > 0) {
                    $raw .= str_repeat(' ', $padding);
                }
                $out .= ' ' . $raw . '|';
            }
            return "<{$color}>{$out}</>";
        };

        $this->output->writeln($border());
        $this->output->writeln($rowLine($headers, 'options=bold;fg=green'));
        $this->output->writeln($border($headers));

        foreach ($rows as $row) {
            $this->output->writeln($rowLine($row, 'fg=white'));
        }

        $this->output->writeln($border());
    }

    /**
     * Display an animated progress bar while $step is executed for each item.
     *
     * @param int $total
     * @param callable(int $index, int $total): void $step
     */
    protected function progress(int $total, callable $step, string $label = 'Processing'): void
    {
        if ($total <= 0) {
            return;
        }

        $width = 30;

        for ($i = 0; $i < $total; $i++) {
            $step($i, $total);

            $done   = $i + 1;
            $percent = (int) round($done / $total * 100);
            $filled = (int) round($done / $total * $width);
            $bar    = str_repeat('█', $filled) . str_repeat('░', $width - $filled);

            $this->output->write(
                "\r  <fg=green>[{$bar}]</> <fg=yellow>{$percent}%</> <fg=gray>{$label} ({$done}/{$total})</>"
            );
        }

        $this->output->writeln('');
    }

    /**
     * Show a spinner-style task indicator around a unit of work.
     */
    protected function task(string $title, callable $work): void
    {
        $this->output->write("  <fg=cyan>⟳</> <fg=white>{$title}</> ...");

        $work();

        $this->output->writeln("\r  <fg=green>✔</> <fg=white>{$title}</>");
    }

    // ── Interactive helpers ──────────────────────────────────────────────────

    protected function ask(string $question, ?string $default = null): string
    {
        $prompt = $default !== null
            ? "{$question} <fg=gray>[{$default}]</>"
            : $question;

        $this->output->writeln("<fg=cyan>?</> <fg=white>{$prompt}</>");

        $handle = fopen('php://stdin', 'r');

        if ($handle === false) {
            return $default ?? '';
        }

        $answer = trim(fgets($handle) ?: '');
        fclose($handle);

        return $answer === '' && $default !== null ? $default : $answer;
    }

    protected function secret(string $question): string
    {
        $this->output->writeln("<fg=cyan>?</> <fg=white>{$question} <fg=gray>(hidden)</></>");

        $handle = fopen('php://stdin', 'r');

        if ($handle === false) {
            return '';
        }

        $answer = trim(fgets($handle) ?: '');
        fclose($handle);

        return $answer;
    }

    protected function confirm(string $question, bool $default = false): bool
    {
        $hint = $default ? '<fg=gray>[Y/n]</>' : '<fg=gray>[y/N]</>';

        $this->output->write("<fg=cyan>?</> <fg=white>{$question}</> {$hint} ");

        $handle = fopen('php://stdin', 'r');

        if ($handle === false) {
            return $default;
        }

        $answer = strtolower(trim(fgets($handle) ?: ''));
        fclose($handle);

        if ($answer === '') {
            return $default;
        }

        return in_array($answer, ['y', 'yes', '1', 'true', 'o', 'oui'], true);
    }

    /**
     * @param string[] $choices
     */
    protected function choice(string $question, array $choices, ?int $default = null): string
    {
        $this->output->writeln("<fg=cyan>?</> <fg=white>{$question}</>");

        foreach ($choices as $key => $label) {
            $this->output->writeln("  <fg=yellow>[" . ($key + 1) . "]</> <fg=white>{$label}</>");
        }

        $fallback = $default !== null ? (string) ($default + 1) : '';
        $answer   = $this->ask('Select', $fallback);

        if (is_numeric($answer)) {
            $index = (int) $answer - 1;
            if (isset($choices[$index])) {
                return $choices[$index];
            }
        }

        return $answer;
    }

    // ── File generation helper ───────────────────────────────────────────────

    /**
     * Write a generated file, guarding against accidental overwrites.
     * Honors the --force flag and prompts for confirmation otherwise.
     */
    protected function writeGenerated(string $file, string $content, string $label): bool
    {
        $relative = $this->relativePath($file);

        if (file_exists($file)) {
            if ($this->option('force')) {
                $this->warn("Overwriting existing {$label}: {$relative}");
            } elseif (!$this->confirm("{$label} {$relative} already exists. Overwrite?")) {
                $this->warn("Cancelled. {$label} was not overwritten.");
                return false;
            }
        }

        $this->write($file, $content);
        $this->success("{$label} created: {$relative}");

        return true;
    }

    protected function relativePath(string $file): string
    {
        $base = rtrim(BASE_PATH, '/') . '/';

        if (str_starts_with($file, $base)) {
            return substr($file, strlen($base));
        }

        return $file;
    }

    protected function autoDiscoverEnabled(): bool
    {
        $flag = $_ENV['PACKAGE_AUTO_DISCOVER'] ?? getenv('PACKAGE_AUTO_DISCOVER') ?? 'true';

        return $flag !== 'false' && $flag !== '0';
    }

    // ── Metadata ─────────────────────────────────────────────────────────────

    public function getDescription(): string
    {
        return $this->description;
    }

    // ── Option parsing ───────────────────────────────────────────────────────

    protected function option(string $key)
    {
        return $this->options[$key] ?? null;
    }

    protected function parseOptions(): void
    {
        $argv = isset($_SERVER['argv']) ? array_slice($_SERVER['argv'], 2) : [];

        $count = count($argv);

        for ($i = 0; $i < $count; $i++) {
            $arg = $argv[$i];

            if (strpos($arg, '--') !== 0) {
                continue;
            }

            $body = substr($arg, 2);

            if (str_contains($body, '=')) {
                [$key, $value] = explode('=', $body, 2);
                $this->options[$key] = $value;
            } elseif ($i + 1 < $count && strpos($argv[$i + 1], '--') !== 0) {
                $this->options[$body] = $argv[$i + 1];
                $i++;
            } else {
                $this->options[$body] = true;
            }
        }
    }

    /**
     * Call another console command.
     */
    protected function call(string $command, array $arguments = []): void
    {
        $argv = array_merge(['zero', $command], $arguments);

        $previous = $_SERVER['argv'] ?? [];
        $_SERVER['argv'] = $argv;

        try {
            (new Console())->run($argv);
        } finally {
            $_SERVER['argv'] = $previous;
        }
    }
}
