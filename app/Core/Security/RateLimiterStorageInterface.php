<?php

declare(strict_types=1);

namespace App\Core\Security;

/**
 * Contract for rate limiter storage backends.
 *
 * Implement this interface to provide custom storage for rate limiting
 * (e.g., Redis, Memcached, database, or in-memory stores).
 */
interface RateLimiterStorageInterface
{
    /**
     * Get the current attempt count for a key.
     *
     * @param string $key The rate limiter key.
     * @return int|null The current count, or null if the key does not exist.
     */
    public function get(string $key): ?int;

    /**
     * Set the attempt count for a key with a TTL.
     *
     * @param string $key     The rate limiter key.
     * @param int    $value   The attempt count to store.
     * @param int    $ttl     Time-to-live in seconds.
     * @return void
     */
    public function set(string $key, int $value, int $ttl): void;

    /**
     * Increment the attempt count for a key.
     *
     * @param string $key The rate limiter key.
     * @return int The new count after incrementing.
     */
    public function increment(string $key): int;

    /**
     * Remove a key from storage.
     *
     * @param string $key The rate limiter key.
     * @return void
     */
    public function forget(string $key): void;
}
