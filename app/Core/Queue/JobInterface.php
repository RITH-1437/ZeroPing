<?php

declare(strict_types=1);

namespace App\Core\Queue;

/**
 * Contract that all queue jobs must implement.
 *
 * Defines the essential lifecycle methods for a queued job,
 * including execution, failure handling, and serialization.
 */
interface JobInterface
{
    /**
     * Execute the job.
     *
     * This method contains the actual work to be performed when
     * the job is processed by a queue worker.
     *
     * @return void
     */
    public function handle(): void;

    /**
     * Handle a job failure.
     *
     * Called when the job has exceeded its maximum retry attempts
     * and is being moved to the failed jobs table.
     *
     * @param \Throwable $exception The exception that caused the failure.
     * @return void
     */
    public function failed(\Throwable $exception): void;

    /**
     * Get the unique identifier for this job instance.
     *
     * @return int|null The job ID, or null if not yet persisted.
     */
    public function id(): ?int;

    /**
     * Get the number of times this job has been attempted.
     *
     * @return int The number of attempts made.
     */
    public function attempts(): int;

    /**
     * Get the maximum number of times the job may be attempted.
     *
     * @return int
     */
    public function getMaxTries(): int;

    /**
     * Get the number of seconds the job can run before timing out.
     *
     * @return int
     */
    public function getTimeout(): int;

    /**
     * Get the number of seconds to delay before the job is available.
     *
     * @return int
     */
    public function getDelay(): int;

    /**
     * Get the queue name this job should be dispatched to.
     *
     * @return string|null The queue name, or null for the default queue.
     */
    public function getQueue(): ?string;

    /**
     * Serialize the job to a JSON payload string.
     *
     * @return string JSON-encoded payload representing this job.
     */
    public function toPayload(): string;
}
