<?php

namespace App\Core\Console\Prompts;

use App\Core\Console\ConsoleStyle;

/**
 * Shared foundation for the interactive CLI prompts used across ZeroPing's
 * wizards (notably `php zero new`). Keeping prompt logic in small,
 * reusable classes means adding a new question to any wizard is a one-liner.
 *
 * Three concrete prompts are provided out of the box:
 *   - Prompt        : free-form text input (with optional default)
 *   - ChoicePrompt  : single-select list with arrow-key navigation
 *   - ConfirmPrompt : yes / no question
 *
 * Every prompt renders through {@see ConsoleStyle} so output automatically
 * follows ZeroPing's colour and formatting conventions.
 */
abstract class BasePrompt
{
    protected ConsoleStyle $output;

    protected string $label;

    protected ?string $default = null;

    /**
     * A single STDIN handle shared by every prompt instance, so that
     * sequential questions read successive lines instead of each
     * re-opening the stream at offset zero.
     */
    private static $stdinHandle = null;

    public function __construct(string $label, ?string $default = null)
    {
        $this->output = new ConsoleStyle();
        $this->label = $label;
        $this->default = $default;
    }

    /**
     * Run the prompt and return the validated answer.
     */
    abstract public function prompt(): mixed;

    protected function write(string $text): void
    {
        $this->output->write($text);
    }

    protected function writeln(string $text = ''): void
    {
        $this->output->writeln($text);
    }

    /**
     * Lazily open (and reuse) the shared STDIN stream.
     */
    protected function stdin()
    {
        if (self::$stdinHandle === null) {
            self::$stdinHandle = fopen('php://stdin', 'r');
        }

        return self::$stdinHandle;
    }

    /**
     * Read a single trimmed line from STDIN.
     *
     * Falls back to the default (or empty string) when STDIN is not
     * available or already exhausted — this keeps the prompts usable
     * in non-interactive pipelines and CI instead of hanging.
     */
    protected function readLine(): string
    {
        $handle = $this->stdin();

        if ($handle === false) {
            return $this->default ?? '';
        }

        $line = fgets($handle);

        if ($line === false) {
            return $this->default ?? '';
        }

        return trim($line);
    }

    protected function defaultHint(): string
    {
        return $this->default !== null
            ? " <fg=gray>[{$this->default}]</>"
            : '';
    }
}
