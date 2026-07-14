<?php

namespace App\Core\Queue\Drivers;

use App\Core\Queue\Job;

class ArrayDriver implements QueueDriver
{
    protected array $jobs = [];

    public function push(Job $job, ?string $queue = null): void
    {
        $this->jobs[$queue][] = $job;
    }

    public function later(int $delay, Job $job, ?string $queue = null): void
    {
        $this->jobs[$queue][] = $job;
    }

    public function pop(?string $queue = null): ?Job
    {
        return array_shift($this->jobs[$queue]);
    }

    public function delete(Job $job): void
    {
        //
    }

    public function release(Job $job, int $delay = 0): void
    {
        //
    }
}
