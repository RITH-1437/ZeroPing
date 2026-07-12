<?php

namespace Zeroping\Queue\Contracts;

/**
 * A unit of work pushed to and popped from a Queue.
 */
interface Job
{
    /**
     * Process the job.
     */
    public function handle(): void;

    /**
     * Called when the job throws (for retries / logging).
     */
    public function failed(\Throwable $e): void;
}
