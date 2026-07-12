<?php

namespace Zeroping\Queue;

use Zeroping\Queue\Contracts\Job;
use Zeroping\Queue\Contracts\Queue;

/**
 * Placeholder synchronous queue: jobs run immediately on push().
 *
 * Demonstrates the Queue contract; replace with a persistent driver in real use.
 */
class SyncQueue implements Queue
{
    public function push(Job $job, ?string $queue = null): void
    {
        $job->handle();
    }

    public function pop(?string $queue = null): ?Job
    {
        return null; // sync queue has no backlog
    }

    public function later(Job $job, int $seconds): void
    {
        sleep($seconds);
        $job->handle();
    }
}
