<?php

declare(strict_types=1);

namespace App\Core\Cache\Drivers;

/**
 * Null cache driver (black-hole).
 *
 * All write operations succeed silently and all read operations return the
 * default value. Useful for disabling caching in specific environments.
 */
class NullCacheDriver implements CacheDriver
{
    /**
     * Retrieve an item from the cache (always returns default).
     *
     * @param string $key     The cache key (unused).
     * @param mixed  $default The default value to return.
     *
     * @return mixed The default value.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $default;
    }

    /**
     * Store an item in the cache (no-op).
     *
     * @param string $key     The cache key (unused).
     * @param mixed  $value   The value to store (unused).
     * @param int    $seconds Time-to-live in seconds (unused).
     *
     * @return bool Always returns true.
     */
    public function put(string $key, mixed $value, int $seconds): bool
    {
        return true;
    }

    /**
     * Determine if an item exists in the cache (always false).
     *
     * @param string $key The cache key (unused).
     *
     * @return bool Always returns false.
     */
    public function has(string $key): bool
    {
        return false;
    }

    /**
     * Remove an item from the cache (no-op).
     *
     * @param string $key The cache key (unused).
     *
     * @return bool Always returns true.
     */
    public function forget(string $key): bool
    {
        return true;
    }

    /**
     * Remove all items from the cache (no-op).
     *
     * @return bool Always returns true.
     */
    public function flush(): bool
    {
        return true;
    }

    /**
     * Execute the callback and return the result without caching.
     *
     * @param string   $key      The cache key (unused).
     * @param int      $seconds  Time-to-live in seconds (unused).
     * @param callable $callback The callback to execute.
     *
     * @return mixed The callback result.
     */
    public function remember(string $key, int $seconds, callable $callback): mixed
    {
        return $callback();
    }

    /**
     * Increment the value of a cache item (always fails).
     *
     * @param string $key   The cache key (unused).
     * @param int    $value The amount to increment by (unused).
     *
     * @return int|false Always returns false.
     */
    public function increment(string $key, int $value = 1): int|false
    {
        return false;
    }

    /**
     * Decrement the value of a cache item (always fails).
     *
     * @param string $key   The cache key (unused).
     * @param int    $value The amount to decrement by (unused).
     *
     * @return int|false Always returns false.
     */
    public function decrement(string $key, int $value = 1): int|false
    {
        return false;
    }

    /**
     * Store an item in the cache indefinitely (no-op).
     *
     * @param string $key   The cache key (unused).
     * @param mixed  $value The value to store (unused).
     *
     * @return bool Always returns true.
     */
    public function forever(string $key, mixed $value): bool
    {
        return true;
    }
}
