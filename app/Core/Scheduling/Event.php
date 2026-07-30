<?php

declare(strict_types=1);

namespace App\Core\Scheduling;

use App\Core\Support\Log;

/**
 * Base class for scheduled task events.
 *
 * NOTE: This class is named "Event" within the Scheduling namespace to represent
 * a scheduled occurrence/task. It is NOT related to {@see \App\Core\Events\Event}
 * which represents application-level domain events. The full namespace
 * `App\Core\Scheduling\Event` makes the distinction clear.
 *
 * Provides the foundation for all schedulable tasks including command execution,
 * callback invocation, and job dispatching. Includes configuration for overlap
 * prevention, environment restrictions, conditional execution, and time
 * constraints.
 *
 * @see CommandEvent For scheduling shell commands.
 * @see CallbackEvent For scheduling PHP callbacks.
 * @see JobEvent For scheduling queue jobs.
 */
abstract class Event
{
    use Frequency;

    /**
     * The command or description for this scheduled task.
     *
     * @var string
     */
    public string $command = '';

    /**
     * Additional parameters for the command.
     *
     * @var array<int, string>
     */
    public array $parameters = [];

    /**
     * Whether this task should run in a background process.
     *
     * @var bool
     */
    public bool $runInBackground = false;

    /**
     * Whether to prevent overlapping executions of this task.
     *
     * @var bool
     */
    public bool $withoutOverlapping = false;

    /**
     * The number of minutes the mutex should remain valid.
     *
     * Only relevant when `withoutOverlapping` is true.
     *
     * @var int
     */
    public int $expiresAt = 1440;

    /**
     * Whether to restrict execution to a single server.
     *
     * @var bool
     */
    public bool $onOneServer = false;

    /**
     * Custom mutex name for overlap prevention.
     *
     * @var string|null
     */
    public ?string $mutexName = null;

    /**
     * The timezone for evaluating the cron schedule.
     *
     * @var string|null
     */
    public ?string $timezone = null;

    /**
     * Restrict execution to specific application environments.
     *
     * @var array<int, string>
     */
    public array $environments = [];

    /**
     * Truth-test callbacks that must all pass for the task to run.
     *
     * @var array<int, callable>
     */
    protected array $when = [];

    /**
     * Skip-test callbacks — if any return true, the task is skipped.
     *
     * @var array<int, callable>
     */
    protected array $skip = [];

    /**
     * Time windows during which the task should run.
     *
     * Each entry is [start, end] in "HH:MM" format.
     *
     * @var array<int, array{0: string, 1: string}>
     */
    protected array $between = [];

    /**
     * Time windows during which the task should NOT run.
     *
     * Each entry is [start, end] in "HH:MM" format.
     *
     * @var array<int, array{0: string, 1: string}>
     */
    protected array $unlessBetween = [];

    /**
     * A human-readable description for this task.
     *
     * @var string|null
     */
    public ?string $description = null;

    /**
     * Create a new scheduled event instance.
     *
     * @param string $command The command or description for this task.
     */
    public function __construct(string $command = '')
    {
        $this->command = $command;
    }

    /**
     * Execute the scheduled task.
     *
     * @return void
     */
    abstract public function run(): void;

    /**
     * Prevent the task from overlapping with a previous instance.
     *
     * When enabled, a mutex is acquired before execution and released after.
     * If the mutex already exists (previous run still active), the task is skipped.
     *
     * @param int $expiresAt The mutex TTL in minutes (default: 1440 = 24 hours).
     * @return static
     */
    public function withoutOverlapping(int $expiresAt = 1440): static
    {
        $this->withoutOverlapping = true;
        $this->expiresAt = $expiresAt;

        return $this;
    }

    /**
     * Restrict the task to run on only one server in a multi-server deployment.
     *
     * Requires a shared cache driver (e.g., Redis, database) for coordination.
     *
     * @return static
     */
    public function onOneServer(): static
    {
        $this->onOneServer = true;

        return $this;
    }

    /**
     * Mark the task to run in a background process.
     *
     * @return static
     */
    public function runInBackground(): static
    {
        $this->runInBackground = true;

        return $this;
    }

    /**
     * Restrict the task to specific application environments.
     *
     * @param string|array<int, string> ...$environments One or more environment names.
     * @return static
     */
    public function environments(string|array ...$environments): static
    {
        $this->environments = [];
        foreach ($environments as $env) {
            if (is_array($env)) {
                $this->environments = array_merge($this->environments, $env);
            } else {
                $this->environments[] = $env;
            }
        }

        return $this;
    }

    /**
     * Add a truth-test callback. The task only runs if ALL when() callbacks return true.
     *
     * @param callable $callback A callback returning bool.
     * @return static
     */
    public function when(callable $callback): static
    {
        $this->when[] = $callback;

        return $this;
    }

    /**
     * Add a skip-test callback. The task is skipped if ANY skip() callback returns true.
     *
     * @param callable $callback A callback returning bool.
     * @return static
     */
    public function skip(callable $callback): static
    {
        $this->skip[] = $callback;

        return $this;
    }

    /**
     * Restrict the task to only run between two times of day.
     *
     * @param string $start Start time in "HH:MM" format.
     * @param string $end End time in "HH:MM" format.
     * @return static
     */
    public function between(string $start, string $end): static
    {
        $this->between[] = [$start, $end];

        return $this;
    }

    /**
     * Restrict the task to NOT run between two times of day.
     *
     * @param string $start Start time in "HH:MM" format.
     * @param string $end End time in "HH:MM" format.
     * @return static
     */
    public function unlessBetween(string $start, string $end): static
    {
        $this->unlessBetween[] = [$start, $end];

        return $this;
    }

    /**
     * Set the timezone for the task's schedule evaluation.
     *
     * @param string $timezone A valid PHP timezone identifier (e.g., 'America/New_York').
     * @return static
     */
    public function timezone(string $timezone): static
    {
        $this->timezone = $timezone;

        return $this;
    }

    /**
     * Set a human-readable description for the task.
     *
     * @param string $description The task description.
     * @return static
     */
    public function description(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Determine if the task is due to run based on its cron expression.
     *
     * Uses the configured timezone if set, otherwise defaults to the system timezone.
     *
     * @return bool True if the cron expression matches the current time.
     */
    public function isDue(): bool
    {
        $date = $this->timezone !== null
            ? new \DateTimeImmutable('now', new \DateTimeZone($this->timezone))
            : new \DateTimeImmutable();

        return (new CronExpression($this->expression))->isDue($date);
    }

    /**
     * Determine if all filter conditions pass for the task.
     *
     * Checks truth-test callbacks, skip callbacks, between constraints,
     * and unless-between constraints.
     *
     * @return bool True if all conditions allow the task to run.
     */
    public function filtersPass(): bool
    {
        // Check truth-test callbacks
        foreach ($this->when as $callback) {
            if (!$callback()) {
                return false;
            }
        }

        // Check skip callbacks
        foreach ($this->skip as $callback) {
            if ($callback()) {
                return false;
            }
        }

        // Check time-between constraints
        if (!empty($this->between) && !$this->isInTimeWindow($this->between)) {
            return false;
        }

        // Check unless-between constraints
        if (!empty($this->unlessBetween) && $this->isInTimeWindow($this->unlessBetween)) {
            return false;
        }

        return true;
    }

    /**
     * Get the mutex name for overlap prevention.
     *
     * @return string A unique identifier for this scheduled task.
     */
    public function getMutexName(): string
    {
        return $this->mutexName ?: 'framework/schedule-' . sha1($this->expression . $this->command);
    }

    /**
     * Check if the current time falls within any of the given time windows.
     *
     * @param array<int, array{0: string, 1: string}> $windows Time windows as [start, end] pairs.
     * @return bool True if current time is within any window.
     */
    protected function isInTimeWindow(array $windows): bool
    {
        $now = new \DateTimeImmutable(
            'now',
            $this->timezone !== null ? new \DateTimeZone($this->timezone) : null
        );
        $currentTime = $now->format('H:i');

        foreach ($windows as [$start, $end]) {
            if ($start <= $end) {
                // Normal window (e.g., 09:00 - 17:00)
                if ($currentTime >= $start && $currentTime <= $end) {
                    return true;
                }
            } else {
                // Overnight window (e.g., 22:00 - 06:00)
                if ($currentTime >= $start || $currentTime <= $end) {
                    return true;
                }
            }
        }

        return false;
    }
}
