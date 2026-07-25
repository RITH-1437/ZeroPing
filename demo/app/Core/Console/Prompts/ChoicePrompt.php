<?php

namespace App\Core\Console\Prompts;

/**
 * Single-select list prompt with arrow-key navigation.
 *
 *   Project Type:
 *   <fg=green>❯</> <fg=white>MVC</>
 *     <fg=gray>API</>
 *     <fg=gray>Empty</>
 *     <fg=gray>Blog</>
 *
 * When STDIN is a real interactive terminal, ↑ / ↓ (or j / k) move
 * the highlighted entry and Enter confirms. Otherwise (piped input,
 * CI, redirected files) the same list is shown and the selection is
 * made by typing a 1-based number or the first letter of a choice
 * and pressing Enter — keeping the prompt reliable everywhere.
 *
 * Esc / Ctrl+C cancels (returns the default).
 */
class ChoicePrompt extends BasePrompt
{
    /** @var string[] */
    private array $choices;

    private int $index;

    /**
     * @param string[] $choices
     */
    public function __construct(string $label, array $choices, ?int $defaultIndex = 0)
    {
        parent::__construct($label);
        $this->choices = array_values($choices);
        $this->index = $this->clamp($defaultIndex ?? 0);
    }

    public function prompt(): string
    {
        $this->render();

        if (function_exists('stream_isatty') && @stream_isatty(STDIN)) {
            $this->enableRaw();
            $this->navigate();
            $this->disableRaw();
        } else {
            $this->index = $this->parseLine($this->readLine());
        }

        $this->writeln('');

        return $this->choices[$this->index];
    }

    private function navigate(): void
    {
        while (true) {
            $key = $this->readKey();

            if ($key === 'up' || $key === 'k') {
                $this->move(-1);
            } elseif ($key === 'down' || $key === 'j') {
                $this->move(1);
            } elseif ($key === 'enter' || $key === 'ctrl_c' || $key === 'escape') {
                return;
            } elseif (str_starts_with($key, 'number:')) {
                $n = (int) substr($key, 8) - 1;
                if (isset($this->choices[$n])) {
                    $this->index = $n;
                    return;
                }
            } elseif (str_starts_with($key, 'letter:')) {
                $letter = strtolower(substr($key, 7));
                foreach ($this->choices as $i => $choice) {
                    if (strtolower($choice[0] ?? '') === $letter) {
                        $this->index = $i;
                        return;
                    }
                }
            }
        }
    }

    private function parseLine(string $line): int
    {
        $line = trim($line);

        if ($line === '') {
            return $this->index;
        }

        if (ctype_digit($line)) {
            $n = (int) $line - 1;
            if (isset($this->choices[$n])) {
                return $n;
            }
        }

        $letter = strtolower($line[0] ?? '');
        foreach ($this->choices as $i => $choice) {
            if (strtolower($choice[0] ?? '') === $letter) {
                return $i;
            }
        }

        return $this->index;
    }

    private function move(int $delta): void
    {
        $count = count($this->choices);
        $this->index = $this->clamp($this->index + $delta);

        // Repaint the list: move up over the choices, clear each line,
        // redraw, then move back up so the next move repaints cleanly.
        $this->write("\e[{$count}A");
        foreach ($this->choices as $i => $choice) {
            $this->write("\e[K");
            if ($i === $this->index) {
                $this->write("  <fg=green>❯</> <fg=white>{$choice}</>\n");
            } else {
                $this->write("    <fg=gray>{$choice}</>\n");
            }
        }
        $this->write("\e[{$count}A");
    }

    private function render(): void
    {
        $this->writeln(
            "<fg=cyan>?</> <fg=white>{$this->label}</>{$this->defaultHint()}"
        );

        foreach ($this->choices as $i => $choice) {
            if ($i === $this->index) {
                $this->writeln("  <fg=green>❯</> <fg=white>{$choice}</>");
            } else {
                $this->writeln("    <fg=gray>{$choice}</>");
            }
        }
    }

    private function clamp(int $value): int
    {
        $count = count($this->choices);

        if ($count === 0) {
            return 0;
        }

        return ($value % $count + $count) % $count;
    }

    /**
     * Read a single key (or escape sequence) from the shared STDIN.
     *
     * Uses a stream timeout instead of stream_select so the reader
     * never spins at EOF. Returns one of: 'up', 'down', 'enter',
     * 'ctrl_c', 'escape', 'number:X', 'letter:X' or 'other'.
     */
    private function readKey(): string
    {
        $handle = $this->stdin();

        if ($handle === false) {
            return 'enter';
        }

        stream_set_timeout($handle, 0, 100000);
        stream_set_blocking($handle, false);

        $buffer = '';
        $idle = 0;

        while (true) {
            $char = fgetc($handle);

            if ($char === false) {
                if (feof($handle)) {
                    stream_set_blocking($handle, true);
                    $line = fgets($handle);
                    $line = trim((string) $line);
                    if ($line === '') {
                        return 'enter';
                    }
                    return ctype_digit($line)
                        ? 'number:' . $line
                        : 'letter:' . $line;
                }

                $idle++;
                if ($idle > 30) {
                    stream_set_blocking($handle, true);
                    $line = fgets($handle);
                    $line = trim((string) $line);
                    if ($line === '') {
                        return 'enter';
                    }
                    return ctype_digit($line)
                        ? 'number:' . $line
                        : 'letter:' . $line;
                }
                continue;
            }

            $buffer .= $char;

            if ($buffer === "\n" || $buffer === "\r") {
                return 'enter';
            }

            if ($buffer === "\003") {
                return 'ctrl_c';
            }

            if ($buffer === "\e") {
                continue; // wait for the rest of an escape sequence
            }

            if (str_starts_with($buffer, "\e[")) {
                if (str_ends_with($buffer, 'A')) {
                    return 'up';
                }
                if (str_ends_with($buffer, 'B')) {
                    return 'down';
                }
                if (str_ends_with($buffer, 'C')) {
                    return 'right';
                }
                if (str_ends_with($buffer, 'D')) {
                    return 'left';
                }
                if (strlen($buffer) > 6) {
                    return 'other';
                }
                continue;
            }

            if (ctype_digit($buffer)) {
                stream_set_blocking($handle, true);
                $rest = fgets($handle);
                $line = trim($buffer . ((string) $rest));
                return ctype_digit($line) ? 'number:' . $line : 'letter:' . $line;
            }

            return 'letter:' . $buffer;
        }
    }

    /**
     * Enable raw/cbreak input so arrow keys arrive per keystroke.
     */
    private function enableRaw(): void
    {
        if (stripos((string) PHP_OS, 'WIN') === 0) {
            @sapi_windows_vt100_support(STDIN, true);
            @sapi_windows_vt100_support(STDOUT, true);
        } else {
            @exec('stty cbreak -echo 2>/dev/null');
        }
    }

    private function disableRaw(): void
    {
        if (stripos((string) PHP_OS, 'WIN') !== 0) {
            @exec('stty sane 2>/dev/null');
        }
    }
}
