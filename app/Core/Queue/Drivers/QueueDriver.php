<?php

namespace App\Core\Queue\Drivers;

use App\Core\Queue\Job;

interface QueueDriver
{
    public function push(Job $job, string $queue = null): void;

    public function later(int $delay, Job $job, string $queue = null): void;

    public function pop(string $queue = null): ?Job;

    public function delete(Job $job): void;

    public function release(Job $job, int $delay = 0): void;
}
