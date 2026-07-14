<?php

namespace App\Core\Queue;

use App\Core\Queue\Drivers\QueueDriver;

class QueueRepository
{
    protected QueueDriver $driver;

    public function __construct(QueueDriver $driver)
    {
        $this->driver = $driver;
    }

    public function push(Job $job, ?string $queue = null): void
    {
        $this->driver->push($job, $queue);
    }

    public function later(int $delay, Job $job, ?string $queue = null): void
    {
        $this->driver->later($delay, $job, $queue);
    }

    public function pop(?string $queue = null): ?Job
    {
        return $this->driver->pop($queue);
    }

    public function delete(Job $job): void
    {
        $this->driver->delete($job);
    }

    public function release(Job $job, int $delay = 0): void
    {
        $this->driver->release($job, $delay);
    }
}
