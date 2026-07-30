<?php

declare(strict_types=1);

namespace App\Core\Cache\Drivers;

/**
 * In-memory array-based cache driver.
 *
 * Stores cache entries in a PHP array for the duration of the current
 * request/process. Useful for testing and short-lived scripts.
 */
class ArrayCacheDriver implements CacheDriver
{
    /**
     * The in-memory cache storage.
     *
     * @var array<string, array{value: mixed, expire: int}>
     */
    protected array $storage = [];

    /**
     * Retrieve an item from the cache.
     *
     * @param string $key     The cache key to look up.
     * @param mixed  $default The value to return if the key does not exist or is expired.
     *
     * @return mixed The cached value or the default.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        if (!isset($this->storage[$key])) {
            return $default;
        }

        $item = $this->storage[$key];

        if (time() >= $item['expire']) {
            $this->forget($key);
            return $default;
        }

        return $item['value'];
    }

    /**
     * Store an item in the cache for a given number of seconds.
     *
     * @param string $key     The cache key.
     * @param mixed  $value   The value to store.
     * @param int    $seconds Time-to-live in seconds.
     *
     * @return bool Always returns true.
     */
    public function put(string $key, mixed $value, int $seconds): bool
    {
        $this->storage[$key] = [
            'value'  => $value,
            'expire' => time() + $seconds,
        ];

        return true;
    }

    /**
     * Determine if an item exists and has not expired in the cache.
     *
     * @param string $key The cache key.
     *
     * @return bool True if the key exists and is valid.
     */
    public function has(string $key): bool
    {
        if (!isset($this->storage[$key])) {
            return false;
        }

        if (time() >= $this->storage[$key]['expire']) {
            $this->forget($key);
            return false;
        }

        return true;
    }

    /**
     * Remove an item from the cache.
     *
     * @param string $key The cache key to remove.
     *
     * @return bool Always returns true.
     */
    public function forget(string $key): bool
    {
        unset($this->storage[$key]);

        return true;
    }

    /**
     * Remove all items from the cache.
     *
     * @return bool Always returns true.
     */
    public function flush(): bool
    {
        $this->storage = [];

        return true;
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
        $value = $this->get($key);

        if ($value !== null) {
            return $value;
        }

        $value = $callback();

        $this->put($key, $value, $seconds);

        return $value;
    }

    /**
     * Increment the value of a cache item.
     *
     * @param string $key   The cache key.
     * @param int    $value The amount to increment by.
     *
     * @return int|false The new value on success, or false if the key does not exist.
     */
    public function increment(string $key, int $value = 1): int|false
    {
        if (!$this->has($key)) {
            return false;
        }

        $this->storage[$key]['value'] = (int) $this->storage[$key]['value'] + $value;

        return $this->storage[$key]['value'];
    }

    /**
     * Decrement the value of a cache item.
     *
     * @param string $key   The cache key.
     * @param int    $value The amount to decrement by.
     *
     * @return int|false The new value on success, or false if the key does not exist.
     */
    public function decrement(string $key, int $value = 1): int|false
    {
        return $this->increment($key, $value * -1);
    }

    /**
     * Store an item in the cache indefinitely (10-year TTL).
     *
     * @param string $key   The cache key.
     * @param mixed  $value The value to store.
     *
     * @return bool Always returns true.
     */
    public function forever(string $key, mixed $value): bool
    {
        return $this->put($key, $value, 315_360_000);
    }
}
