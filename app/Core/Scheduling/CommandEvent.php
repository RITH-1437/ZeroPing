<?php

declare(strict_types=1);

namespace App\Core\Scheduling;

/**
 * A scheduled task that executes a shell command.
 *
 * Runs the specified command via `exec()`, captures output and exit code,
 * and throws a RuntimeException on non-zero exit codes so the scheduler
 * can log the failure.
 *
 * @example
 * ```php
 * $event = new CommandEvent('php artisan cache:clear');
 * $event->dailyAt('03:00')->withoutOverlapping();
 * ```
 */
class CommandEvent extends Event
{
    /**
     * The captured output from the last execution.
     *
     * @var array<int, string>
     */
    protected array $output = [];

    /**
     * The exit code from the last execution.
     *
     * @var int|null
     */
    protected ?int $exitCode = null;

    /**
     * Create a new command event instance.
     *
     * @param string $command The shell command to execute.
     */
    public function __construct(string $command)
    {
        parent::__construct($command);
    }

    /**
     * Execute the scheduled shell command.
     *
     * Uses `exec()` to run the command and captures both the output
     * and exit code. A non-zero exit code results in a RuntimeException
     * being thrown so the scheduler can handle the failure.
     *
     * @return void
     *
     * @throws \RuntimeException When the command exits with a non-zero code.
     */
    public function run(): void
    {
        $this->output = [];
        $this->exitCode = null;

        $command = $this->buildCommand();

        exec($command, $this->output, $this->exitCode);

        if ($this->exitCode !== 0) {
            throw new \RuntimeException(
                sprintf(
                    'Scheduled command "%s" failed with exit code %d: %s',
                    $command,
                    $this->exitCode,
                    implode("\n", $this->output)
                )
            );
        }
    }

    /**
     * Build the full command string including any parameters.
     *
     * @return string The complete command to execute.
     */
    protected function buildCommand(): string
    {
        if (empty($this->parameters)) {
            return $this->command;
        }

        return $this->command . ' ' . implode(' ', array_map('escapeshellarg', $this->parameters));
    }

    /**
     * Get the output from the last execution.
     *
     * @return array<int, string> The output lines.
     */
    public function getOutput(): array
    {
        return $this->output;
    }

    /**
     * Get the exit code from the last execution.
     *
     * @return int|null The exit code, or null if not yet executed.
     */
    public function getExitCode(): ?int
    {
        return $this->exitCode;
    }
}
