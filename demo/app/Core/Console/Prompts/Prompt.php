<?php

namespace App\Core\Console\Prompts;

/**
 * Free-form text prompt.
 *
 *   Project Name:
 *   >
 *
 * Returns the trimmed user input, or the supplied default when the
 * user simply presses enter.
 */
class Prompt extends BasePrompt
{
    public function prompt(): string
    {
        $this->writeln(
            "<fg=cyan>?</> <fg=white>{$this->label}</>{$this->defaultHint()}"
        );

        $answer = $this->readLine();

        return $answer === '' && $this->default !== null
            ? $this->default
            : $answer;
    }
}
