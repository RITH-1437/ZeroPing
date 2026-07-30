<?php

declare(strict_types=1);

namespace App\Core\Scheduling;

/**
 * Contract for scheduler mutex implementations.
 *
 * A mutex prevents overlapping execution of scheduled tasks.
 * Implementations should use a shared storage backend (cache, database,
 * file locks) that is accessible by all servers in a multi-server deployment.
 *
 * @see Scheduler::runEvent() Where the mutex is acquired and released.
 */
interface Mutex
{
    /**
     * Attempt to acquire the mutex for a scheduled event.
     *
     * @param Event $event The scheduled event to acquire the mutex for.
     * @return bool True if the mutex was successfully acquired, false if already held.
     */
    public function create(Event $event): bool;

    /**
     * Check if a mutex exists (is currently held) for a scheduled event.
     *
     * @param Event $event The scheduled event to check.
     * @return bool True if the mutex exists (event is currently running).
     */
    public function exists(Event $event): bool;

    /**
     * Release the mutex for a scheduled event.
     *
     * @param Event $event The scheduled event to release the mutex for.
     * @return void
     */
    public function forget(Event $event): void;
}
