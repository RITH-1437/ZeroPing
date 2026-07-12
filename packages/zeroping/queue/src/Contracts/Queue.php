<?php

namespace Zeroping\Queue\Contracts;

/**
 * A single queue connection (sync, database, redis, ...).
 */
interface Queue
{
    /**
     * Push a job onto the queue.
     */
    public function push(Job $job, ?string $queue = null): void;

    /**
     * Pop the next job, or null if the queue is empty.
     */
    public function pop(?string $queue = null): ?Job;

    /**
     * Push a job to be run after $seconds.
     */
    public function later(Job $job, int $seconds): void;
}
