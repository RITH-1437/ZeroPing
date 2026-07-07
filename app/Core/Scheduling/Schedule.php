<?php

namespace App\Core\Scheduling;

use App\Core\Queue\Job;

class Schedule
{
    protected array $events = [];

    public function command(string $command): CommandEvent
    {
        return $this->events[] = new CommandEvent($command);
    }

    public function job(Job $job): JobEvent
    {
        return $this->events[] = new JobEvent($job);
    }

    public function call(callable $callback): CallbackEvent
    {
        return $this->events[] = new CallbackEvent($callback);
    }

    public function events(): array
    {
        return $this->events;
    }
}
