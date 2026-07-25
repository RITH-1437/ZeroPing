<?php

namespace App\Core\Console\Prompts;

/**
 * Yes / no confirmation prompt.
 *
 *   Install Authentication? <fg=gray>[Y/n]</>
 *
 * Returns true for yes-style answers (y, yes, 1, true, o, oui) and
 * honours the boolean $default when the user just presses enter.
 */
class ConfirmPrompt extends BasePrompt
{
    private bool $defaultBool;

    public function __construct(string $label, bool $default = false)
    {
        parent::__construct($label, $default ? 'y' : 'n');
        $this->defaultBool = $default;
    }

    public function prompt(): bool
    {
        $hint = $this->defaultBool
            ? '<fg=gray>[Y/n]</>'
            : '<fg=gray>[y/N]</>';

        $this->writeln("<fg=cyan>?</> <fg=white>{$this->label}</> {$hint} ");

        $answer = strtolower($this->readLine());

        if ($answer === '') {
            return $this->defaultBool;
        }

        return in_array($answer, ['y', 'yes', '1', 'true', 'o', 'oui'], true);
    }
}
