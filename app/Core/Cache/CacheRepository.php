<?php

declare(strict_types=1);

namespace App\Core\Cache;

use App\Core\Cache\Drivers\CacheDriver;

/**
 * Cache repository wrapping a CacheDriver with a per-request in-memory layer.
 *
 * Within a single request the same cache key is frequently read many times
 * (e.g. config, view paths). The local layer serves those reads from memory,
 * avoiding repeated I/O and deserialization in the underlying driver.
 *
 * Each local entry stores its value alongside an absolute expiry timestamp
 * so expired items are transparently dropped, mirroring the driver's TTL.
 *
 * @since 1.0.0
 * @author Rin Nairith
 * @link https://zero-ping.duckdns.org/docs/caching
 */
class CacheRepository
{
    /**
     * The underlying cache driver implementation.
     */
    protected CacheDriver $driver;

    /**
     * Per-request in-memory cache.
     *
     * @var array<string, array{value: mixed, expires: int}>
     */
    protected array $local = [];

    /**
     * Create a new cache repository instance.
     *
     * @param CacheDriver $driver The cache driver to wrap.
     */
    public function __construct(CacheDriver $driver)
    {
        $this->driver = $driver;
    }

    /**
     * Retrieve an item from the cache.
     *
     * Checks the local memory layer first, then falls back to the driver.
     *
     * @param string $key     The cache key.
     * @param mixed  $default The default value if the key is not found.
     *
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed
    {
        if (array_key_exists($key, $this->local)) {
            if (time() >= $this->local[$key]['expires']) {
                unset($this->local[$key]);
            } else {
                return $this->local[$key]['value'];
            }
        }

        return $this->driver->get($key, $default);
    }

    /**
     * Store an item in the cache.
     *
     * Stores in both the local memory layer and the underlying driver.
     *
     * @param string $key     The cache key.
     * @param mixed  $value   The value to store.
     * @param int    $seconds Time-to-live in seconds.
     *
     * @return bool True on success.
     */
    public function put(string $key, mixed $value, int $seconds): bool
    {
        $this->local[$key] = [
            'value'   => $value,
            'expires' => time() + $seconds,
        ];

        return $this->driver->put($key, $value, $seconds);
    }

    /**
     * Get an item from the cache, or execute the callback and store the result.
     *
     * @param string   $key      The cache key.
     * @param int      $seconds  Time-to-live in seconds.
     * @param callable $callback The callback to execute if the key is not found.
     *
     * @return mixed The cached or freshly computed value.
     */
    public function remember(string $key, int $seconds, callable $callback): mixed
    {
        if (array_key_exists($key, $this->local)) {
            if (time() >= $this->local[$key]['expires']) {
                unset($this->local[$key]);
            } else {
                return $this->local[$key]['value'];
            }
        }

        $value = $this->driver->remember($key, $seconds, $callback);

        $this->local[$key] = [
            'value'   => $value,
            'expires' => time() + $seconds,
        ];

        return $value;
    }

    /**
     * Get an item from the cache, or execute the callback and store indefinitely.
     *
     * @param string   $key      The cache key.
     * @param callable $callback The callback to execute if the key is not found.
     *
     * @return mixed The cached or freshly computed value.
     */
    public function rememberForever(string $key, callable $callback): mixed
    {
        if (array_key_exists($key, $this->local)) {
            if (time() >= $this->local[$key]['expires']) {
                unset($this->local[$key]);
            } else {
                return $this->local[$key]['value'];
            }
        }

        $value = $callback();

        $this->driver->forever($key, $value);

        $this->local[$key] = [
            'value'   => $value,
            'expires' => time() + 315_360_000,
        ];

        return $value;
    }

    /**
     * Store an item in the cache indefinitely (10-year TTL).
     *
     * @param string $key   The cache key.
     * @param mixed  $value The value to store.
     *
     * @return bool True on success.
     */
    public function forever(string $key, mixed $value): bool
    {
        $this->local[$key] = [
            'value'   => $value,
            'expires' => time() + 315_360_000,
        ];

        return $this->driver->forever($key, $value);
    }

    /**
     * Determine if an item exists in the cache and has not expired.
     *
     * @param string $key The cache key.
     *
     * @return bool True if the key exists and its value is not null.
     */
    public function has(string $key): bool
    {
        if (array_key_exists($key, $this->local)) {
            if (time() < $this->local[$key]['expires'] && $this->local[$key]['value'] !== null) {
                return true;
            }

            unset($this->local[$key]);
        }

        return $this->driver->has($key);
    }

    /**
     * Remove an item from the cache.
     *
     * @param string $key The cache key to remove.
     *
     * @return bool True on success.
     */
    public function forget(string $key): bool
    {
        unset($this->local[$key]);

        return $this->driver->forget($key);
    }

    /**
     * Remove all items from the cache.
     *
     * @return bool True on success.
     */
    public function flush(): bool
    {
        $this->local = [];

        return $this->driver->flush();
    }

    /**
     * Increment the value of a cache item.
     *
     * Invalidates the local cache for the key to ensure consistency.
     *
     * @param string $key   The cache key.
     * @param int    $value The amount to increment by.
     *
     * @return int|false The new value on success, or false on failure.
     */
    public function increment(string $key, int $value = 1): int|false
    {
        unset($this->local[$key]);

        return $this->driver->increment($key, $value);
    }

    /**
     * Decrement the value of a cache item.
     *
     * Invalidates the local cache for the key to ensure consistency.
     *
     * @param string $key   The cache key.
     * @param int    $value The amount to decrement by.
     *
     * @return int|false The new value on success, or false on failure.
     */
    public function decrement(string $key, int $value = 1): int|false
    {
        unset($this->local[$key]);

        return $this->driver->decrement($key, $value);
    }
}
