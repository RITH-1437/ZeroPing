<?php

declare(strict_types=1);

namespace App\Core\Cache\Drivers;

/**
 * Contract for cache store drivers.
 *
 * All cache drivers must implement this interface to provide a consistent
 * API for storing, retrieving, and managing cached data.
 */
interface CacheDriver
{
    /**
     * Retrieve an item from the cache by its key.
     *
     * @param string $key     The cache key to look up.
     * @param mixed  $default The value to return if the key does not exist.
     *
     * @return mixed The cached value or the default.
     */
    public function get(string $key, mixed $default = null): mixed;

    /**
     * Store an item in the cache for a given number of seconds.
     *
     * @param string $key     The cache key.
     * @param mixed  $value   The value to store.
     * @param int    $seconds Time-to-live in seconds.
     *
     * @return bool True on success, false on failure.
     */
    public function put(string $key, mixed $value, int $seconds): bool;

    /**
     * Determine if an item exists and has not expired in the cache.
     *
     * @param string $key The cache key.
     *
     * @return bool True if the key exists and is not expired.
     */
    public function has(string $key): bool;

    /**
     * Remove an item from the cache.
     *
     * @param string $key The cache key to remove.
     *
     * @return bool True on success, false if the key did not exist.
     */
    public function forget(string $key): bool;

    /**
     * Remove all items from the cache store.
     *
     * @return bool True on success.
     */
    public function flush(): bool;

    /**
     * Get an item from the cache, or execute the given callback and store the result.
     *
     * @param string   $key      The cache key.
     * @param int      $seconds  Time-to-live in seconds.
     * @param callable $callback The callback to execute if the key is not found.
     *
     * @return mixed The cached or freshly computed value.
     */
    public function remember(string $key, int $seconds, callable $callback): mixed;

    /**
     * Increment the value of a cache item.
     *
     * @param string $key   The cache key.
     * @param int    $value The amount to increment by.
     *
     * @return int|false The new value on success, or false on failure.
     */
    public function increment(string $key, int $value = 1): int|false;

    /**
     * Decrement the value of a cache item.
     *
     * @param string $key   The cache key.
     * @param int    $value The amount to decrement by.
     *
     * @return int|false The new value on success, or false on failure.
     */
    public function decrement(string $key, int $value = 1): int|false;

    /**
     * Store an item in the cache indefinitely.
     *
     * @param string $key   The cache key.
     * @param mixed  $value The value to store.
     *
     * @return bool True on success, false on failure.
     */
    public function forever(string $key, mixed $value): bool;
}
