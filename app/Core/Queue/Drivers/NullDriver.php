<?php

declare(strict_types=1);

namespace App\Core\Queue\Drivers;

use App\Core\Queue\Job;

/**
 * Null/no-op queue driver that silently discards all jobs.
 *
 * Useful for environments where queue processing should be completely
 * disabled (e.g., certain test scenarios or development configurations
 * where you want to suppress all background work).
 *
 * No jobs are stored or executed through this driver.
 */
class NullDriver implements QueueDriver
{
    /**
     * {@inheritdoc}
     *
     * Silently discards the job without storing or executing it.
     */
    public function push(Job $job, ?string $queue = null): void
    {
        // Intentionally empty — job is discarded.
    }

    /**
     * {@inheritdoc}
     *
     * Silently discards the job without storing or executing it.
     */
    public function later(int $delay, Job $job, ?string $queue = null): void
    {
        // Intentionally empty — job is discarded.
    }

    /**
     * {@inheritdoc}
     *
     * Always returns null since no jobs are stored.
     */
    public function pop(?string $queue = null): ?Job
    {
        return null;
    }

    /**
     * {@inheritdoc}
     *
     * No-op.
     */
    public function delete(Job $job): void
    {
        // Intentionally empty.
    }

    /**
     * {@inheritdoc}
     *
     * No-op.
     */
    public function release(Job $job, int $delay = 0): void
    {
        // Intentionally empty.
    }

    /**
     * {@inheritdoc}
     *
     * Always returns 0 since no jobs are stored.
     */
    public function size(?string $queue = null): int
    {
        return 0;
    }
}
