<?php

declare(strict_types=1);

namespace App\Core\Queue\Drivers;

use App\Core\Queue\Job;

/**
 * In-memory array queue driver for testing.
 *
 * Stores jobs in a PHP array, making it ideal for unit and integration
 * testing where you need to inspect dispatched jobs without requiring
 * a database or external service. Jobs are lost when the process ends.
 *
 * @example
 * ```php
 * $driver = new ArrayDriver();
 * $driver->push(new MyJob(), 'emails');
 * assert($driver->size('emails') === 1);
 * $job = $driver->pop('emails');
 * ```
 */
class ArrayDriver implements QueueDriver
{
    /**
     * The stored jobs, organized by queue name.
     *
     * @var array<string, array<int, Job>>
     */
    protected array $jobs = [];

    /**
     * {@inheritdoc}
     */
    public function push(Job $job, ?string $queue = null): void
    {
        $queue = $queue ?? 'default';
        $this->jobs[$queue][] = $job;
    }

    /**
     * {@inheritdoc}
     *
     * Note: Delay is ignored in the array driver — the job is immediately
     * available for popping.
     */
    public function later(int $delay, Job $job, ?string $queue = null): void
    {
        $this->push($job, $queue);
    }

    /**
     * {@inheritdoc}
     *
     * Returns and removes the first job from the specified queue.
     */
    public function pop(?string $queue = null): ?Job
    {
        $queue = $queue ?? 'default';

        if (empty($this->jobs[$queue])) {
            return null;
        }

        return array_shift($this->jobs[$queue]);
    }

    /**
     * {@inheritdoc}
     *
     * No-op for the array driver since jobs are removed on pop.
     */
    public function delete(Job $job): void
    {
        // No-op: jobs are already removed from the array on pop.
    }

    /**
     * {@inheritdoc}
     *
     * Releases the job back to the end of the queue.
     */
    public function release(Job $job, int $delay = 0): void
    {
        $queue = $job->getQueue() ?? 'default';
        $this->jobs[$queue][] = $job;
    }

    /**
     * {@inheritdoc}
     */
    public function size(?string $queue = null): int
    {
        $queue = $queue ?? 'default';

        return count($this->jobs[$queue] ?? []);
    }

    /**
     * Get all jobs for a given queue (useful for test assertions).
     *
     * @param string|null $queue The queue name, or null for default.
     * @return array<int, Job> The jobs in the queue.
     */
    public function getJobs(?string $queue = null): array
    {
        $queue = $queue ?? 'default';

        return $this->jobs[$queue] ?? [];
    }

    /**
     * Flush all jobs from all queues.
     *
     * @return void
     */
    public function flush(): void
    {
        $this->jobs = [];
    }
}
