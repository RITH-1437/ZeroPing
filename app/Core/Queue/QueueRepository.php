<?php

declare(strict_types=1);

namespace App\Core\Queue;

use App\Core\Queue\Drivers\QueueDriver;

/**
 * Repository that wraps a queue driver and provides a clean API for queue operations.
 *
 * This class acts as an intermediary between the application code and
 * the underlying queue driver, providing a consistent interface regardless
 * of which driver is being used.
 *
 * @example
 * ```php
 * $repository = new QueueRepository(new DatabaseDriver($config));
 * $repository->push($job, 'emails');
 * $next = $repository->pop('emails');
 * ```
 */
class QueueRepository
{
    /**
     * The underlying queue driver instance.
     *
     * @var QueueDriver
     */
    protected QueueDriver $driver;

    /**
     * Create a new queue repository instance.
     *
     * @param QueueDriver $driver The queue driver to use for storage operations.
     */
    public function __construct(QueueDriver $driver)
    {
        $this->driver = $driver;
    }

    /**
     * Push a job onto the queue.
     *
     * @param Job $job The job to push onto the queue.
     * @param string|null $queue The queue name, or null for the default queue.
     * @return void
     */
    public function push(Job $job, ?string $queue = null): void
    {
        $this->driver->push($job, $queue);
    }

    /**
     * Push a job onto the queue after a delay.
     *
     * The job will not be available for processing until the specified
     * number of seconds has elapsed.
     *
     * @param int $delay The delay in seconds before the job becomes available.
     * @param Job $job The job to push onto the queue.
     * @param string|null $queue The queue name, or null for the default queue.
     * @return void
     */
    public function later(int $delay, Job $job, ?string $queue = null): void
    {
        $this->driver->later($delay, $job, $queue);
    }

    /**
     * Pop the next job from the queue.
     *
     * Returns the next available job that is ready for processing,
     * or null if no jobs are available.
     *
     * @param string|null $queue The queue name, or null for the default queue.
     * @return Job|null The next available job, or null.
     */
    public function pop(?string $queue = null): ?Job
    {
        return $this->driver->pop($queue);
    }

    /**
     * Delete a processed job from the queue.
     *
     * Should be called after a job has been successfully processed
     * to remove it from the queue storage.
     *
     * @param Job $job The job to delete.
     * @return void
     */
    public function delete(Job $job): void
    {
        $this->driver->delete($job);
    }

    /**
     * Release a job back onto the queue.
     *
     * Used when a job needs to be retried. The job is released back
     * with an optional delay before it becomes available again.
     *
     * @param Job $job The job to release.
     * @param int $delay The delay in seconds before the job becomes available again.
     * @return void
     */
    public function release(Job $job, int $delay = 0): void
    {
        $this->driver->release($job, $delay);
    }

    /**
     * Get the underlying queue driver instance.
     *
     * @return QueueDriver
     */
    public function getDriver(): QueueDriver
    {
        return $this->driver;
    }
}
