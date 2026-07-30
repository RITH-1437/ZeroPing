<?php

declare(strict_types=1);

namespace App\Core\Queue\Drivers;

use App\Core\Queue\Job;

/**
 * Synchronous queue driver that executes jobs immediately.
 *
 * Jobs dispatched through this driver are processed inline within the
 * current request/process. No actual queuing occurs — the job's `handle()`
 * method is called directly upon dispatch. Useful for local development,
 * testing, or when background processing is not needed.
 *
 * Note: The `later()` method will sleep for the specified delay before
 * executing, which blocks the current process.
 */
class SyncDriver implements QueueDriver
{
    /**
     * {@inheritdoc}
     *
     * Executes the job immediately in the current process.
     */
    public function push(Job $job, ?string $queue = null): void
    {
        $job->handle();
    }

    /**
     * {@inheritdoc}
     *
     * Sleeps for the specified delay, then executes the job immediately.
     * Warning: This blocks the current process for the duration of the delay.
     */
    public function later(int $delay, Job $job, ?string $queue = null): void
    {
        if ($delay > 0) {
            sleep($delay);
        }

        $this->push($job, $queue);
    }

    /**
     * {@inheritdoc}
     *
     * Always returns null since sync jobs are never queued.
     */
    public function pop(?string $queue = null): ?Job
    {
        return null;
    }

    /**
     * {@inheritdoc}
     *
     * No-op for the sync driver.
     */
    public function delete(Job $job): void
    {
        // No-op: sync jobs are not persisted.
    }

    /**
     * {@inheritdoc}
     *
     * No-op for the sync driver.
     */
    public function release(Job $job, int $delay = 0): void
    {
        // No-op: sync jobs are not persisted.
    }

    /**
     * {@inheritdoc}
     *
     * Always returns 0 since sync jobs are never stored.
     */
    public function size(?string $queue = null): int
    {
        return 0;
    }
}
