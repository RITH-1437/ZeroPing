<?php

declare(strict_types=1);

namespace App\Core\Scheduling;

class CommandEvent extends Event
{
    /**
     * Run the scheduled command.
     *
     * Uses exec() with the command and captures the output and exit code
     * instead of passthru(), which directly outputs to stdout and is
     * susceptible to interruption. The exit code is checked and a
     * RuntimeException is thrown on failure so the scheduler can log it.
     *
     * @throws \RuntimeException When the command exits with a non-zero code.
     */
    public function run(): void
    {
        $command = $this->command;

        exec($command, $output, $exitCode);

        if ($exitCode !== 0) {
            throw new \RuntimeException(
                sprintf(
                    'Scheduled command "%s" failed with exit code %d: %s',
                    $command,
                    $exitCode,
                    implode("\n", $output)
                )
            );
        }
    }
}
