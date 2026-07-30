<?php

declare(strict_types=1);

namespace App\Core\Queue\Drivers;

use App\Core\Queue\Job;

/**
 * Contract for queue storage drivers.
 *
 * All queue drivers must implement this interface, providing the fundamental
 * operations needed to manage jobs in a queue: pushing, popping, deleting,
 * and releasing jobs.
 *
 * Built-in implementations include:
 * - {@see SyncDriver} — Executes jobs immediately (no actual queuing).
 * - {@see DatabaseDriver} — Stores jobs in a database table.
 * - {@see ArrayDriver} — Stores jobs in memory (useful for testing).
 * - {@see NullDriver} — Discards all jobs silently (useful for testing).
 */
interface QueueDriver
{
    /**
     * Push a new job onto the queue.
     *
     * @param Job $job The job instance to push.
     * @param string|null $queue The queue name, or null for the default queue.
     * @return void
     */
    public function push(Job $job, ?string $queue = null): void;

    /**
     * Push a new job onto the queue after a delay.
     *
     * @param int $delay The number of seconds to delay before the job is available.
     * @param Job $job The job instance to push.
     * @param string|null $queue The queue name, or null for the default queue.
     * @return void
     */
    public function later(int $delay, Job $job, ?string $queue = null): void;

    /**
     * Pop the next available job from the queue.
     *
     * Returns the next job that is ready for processing (not reserved,
     * and past its available_at time), or null if no jobs are available.
     *
     * @param string|null $queue The queue name, or null for the default queue.
     * @return Job|null The next available job, or null if queue is empty.
     */
    public function pop(?string $queue = null): ?Job;

    /**
     * Delete a job from the queue after successful processing.
     *
     * @param Job $job The job to delete.
     * @return void
     */
    public function delete(Job $job): void;

    /**
     * Release a job back onto the queue for retry.
     *
     * @param Job $job The job to release.
     * @param int $delay The number of seconds to delay before the job is available again.
     * @return void
     */
    public function release(Job $job, int $delay = 0): void;

    /**
     * Get the size of the queue (number of pending jobs).
     *
     * @param string|null $queue The queue name, or null for the default queue.
     * @return int The number of jobs waiting in the queue.
     */
    public function size(?string $queue = null): int;
}
