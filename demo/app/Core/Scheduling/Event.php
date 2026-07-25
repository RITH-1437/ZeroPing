<?php

namespace App\Core\Scheduling;

use App\Core\Support\Log;

abstract class Event
{
    use Frequency;

    public string $command;
    public array $parameters = [];
    public bool $runInBackground = false;
    public bool $withoutOverlapping = false;
    public int $expiresAt = 1440;
    public ?string $onOneServer = null;
    public ?string $mutexName = null;
    public ?string $timezone = null;
    public array $environments = [];
    public array $when = [];
    public array $skip = [];
    public array $between = [];
    public array $unlessBetween = [];
    public array $weekdays = [];
    public array $weekends = [];

    /**
     * Create a new event instance.
     *
     * @param string $command
     */
    public function __construct(string $command)
    {
        $this->command = $command;
    }

    abstract public function run(): void;

    /**
     * Prevent the event from overlapping with a previous instance.
     *
     * @param int $expiresAt
     * @return self
     */
    public function withoutOverlapping(int $expiresAt = 1440): self
    {
        $this->withoutOverlapping = true;
        $this->expiresAt = $expiresAt;

        return $this;
    }

    /**
     * Only run the event on one server.
     *
     * @return self
     */
    public function onOneServer(): self
    {
        $this->onOneServer = true;

        return $this;
    }

    /**
     * Run the event in the background.
     *
     * @return self
     */
    public function runInBackground(): self
    {
        $this->runInBackground = true;

        return $this;
    }

    /**
     * Restrict the event to the given environments.
     *
     * @param array $environments
     * @return self
     */
    public function environments(array $environments): self
    {
        $this->environments = $environments;

        return $this;
    }

    /**
     * Add a truth test to the event.
     *
     * @param callable $callback
     * @return self
     */
    public function when(callable $callback): self
    {
        $this->when[] = $callback;

        return $this;
    }

    /**
     * Add a skip test to the event.
     *
     * @param callable $callback
     * @return self
     */
    public function skip(callable $callback): self
    {
        $this->skip[] = $callback;

        return $this;
    }

    /**
     * Restrict the event to run between two times.
     *
     * @param string $start
     * @param string $end
     * @return self
     */
    public function between(string $start, string $end): self
    {
        $this->between[] = [$start, $end];

        return $this;
    }

    /**
     * Restrict the event to not run between two times.
     *
     * @param string $start
     * @param string $end
     * @return self
     */
    public function unlessBetween(string $start, string $end): self
    {
        $this->unlessBetween[] = [$start, $end];

        return $this;
    }

    /**
     * Restrict the event to weekdays.
     *
     * @return self
     */
    public function weekdays(): self
    {
        $this->weekdays = [1, 2, 3, 4, 5];

        return $this;
    }

    /**
     * Restrict the event to weekends.
     *
     * @return self
     */
    public function weekends(): self
    {
        $this->weekends = [0, 6];

        return $this;
    }

    /**
     * Set the timezone for the event.
     *
     * @param string $timezone
     * @return self
     */
    public function timezone(string $timezone): self
    {
        $this->timezone = $timezone;

        return $this;
    }

    /**
     * Determine if the event is due to run.
     *
     * @return bool
     */
    public function isDue(): bool
    {
        return (new CronExpression($this->expression))->isDue();
    }

    /**
     * Determine if all truth and skip tests pass.
     *
     * @return bool
     */
    public function filtersPass(): bool
    {
        foreach ($this->when as $callback) {
            if (!$callback()) {
                return false;
            }
        }

        foreach ($this->skip as $callback) {
            if ($callback()) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get the mutex name for the event.
     *
     * @return string
     */
    public function getMutexName(): string
    {
        return $this->mutexName ?: 'framework/schedule-' . sha1($this->expression . $this->command);
    }
}
