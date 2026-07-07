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

    public function __construct(string $command)
    {
        $this->command = $command;
    }

    abstract public function run(): void;

    public function withoutOverlapping(int $expiresAt = 1440): self
    {
        $this->withoutOverlapping = true;
        $this->expiresAt = $expiresAt;

        return $this;
    }

    public function onOneServer(): self
    {
        $this->onOneServer = true;

        return $this;
    }

    public function runInBackground(): self
    {
        $this->runInBackground = true;

        return $this;
    }

    public function environments(array $environments): self
    {
        $this->environments = $environments;

        return $this;
    }

    public function when(callable $callback): self
    {
        $this->when[] = $callback;

        return $this;
    }

    public function skip(callable $callback): self
    {
        $this->skip[] = $callback;

        return $this;
    }

    public function between(string $start, string $end): self
    {
        $this->between[] = [$start, $end];

        return $this;
    }

    public function unlessBetween(string $start, string $end): self
    {
        $this->unlessBetween[] = [$start, $end];

        return $this;
    }

    public function weekdays(): self
    {
        $this->weekdays = [1, 2, 3, 4, 5];

        return $this;
    }

    public function weekends(): self
    {
        $this->weekends = [0, 6];

        return $this;
    }

    public function timezone(string $timezone): self
    {
        $this->timezone = $timezone;

        return $this;
    }

    public function isDue(): bool
    {
        // This is a simplified implementation. A real implementation would
        // use a library like cron-expression.
        return true;
    }

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

    public function getMutexName(): string
    {
        return $this->mutexName ?: 'framework/schedule-' . sha1($this->expression . $this->command);
    }
}
