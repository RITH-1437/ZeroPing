<?php

namespace App\Core\Cache;

use App\Core\Cache\Drivers\CacheDriver;

class CacheRepository
{
    protected CacheDriver $driver;

    /**
     * Per-request in-memory cache.
     *
     * Within a single request the same cache key is frequently read many
     * times (e.g. config, view paths). Hitting memory instead of the file/
     * array driver removes repeated (de)serialization and I/O.
     *
     * Each entry stores its value alongside an absolute expiry timestamp so
     * expired items are transparently dropped, mirroring the driver's TTL.
     *
     * @var array<string, array{value: mixed, expires: int}>
     */
    protected array $local = [];

    /**
     * Create a new cache repository instance.
     *
     * @param CacheDriver $driver
     */
    public function __construct(CacheDriver $driver)
    {
        $this->driver = $driver;
    }

    /**
     * Retrieve an item from the cache.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null)
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
     * @param string $key
     * @param mixed $value
     * @param int $seconds
     * @return bool
     */
    public function put(string $key, $value, int $seconds): bool
    {
        $this->local[$key] = [
            'value' => $value,
            'expires' => time() + $seconds,
        ];

        return $this->driver->put($key, $value, $seconds);
    }

    /**
     * Get an item or store the result of a callback.
     *
     * @param string $key
     * @param int $seconds
     * @param callable $callback
     * @return mixed
     */
    public function remember(string $key, int $seconds, callable $callback)
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
            'value' => $value,
            'expires' => time() + $seconds,
        ];

        return $value;
    }

    /**
     * Get an item or store the result of a callback indefinitely.
     *
     * @param string $key
     * @param callable $callback
     * @return mixed
     */
    public function rememberForever(string $key, callable $callback)
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
            'value' => $value,
            'expires' => time() + 315360000,
        ];

        return $value;
    }

    /**
     * Store an item in the cache indefinitely.
     *
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    public function forever(string $key, mixed $value): bool
    {
        // Store with a very long TTL (10 years)
        $this->local[$key] = [
            'value' => $value,
            'expires' => time() + 315360000,
        ];

        return $this->driver->put($key, $value, 315360000);
    }

    /**
     * Determine if an item exists in the cache.
     *
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        if (array_key_exists($key, $this->local)) {
            return time() < $this->local[$key]['expires']
                && $this->local[$key]['value'] !== null;
        }

        return $this->driver->has($key);
    }

    /**
     * Remove an item from the cache.
     *
     * @param string $key
     * @return bool
     */
    public function forget(string $key): bool
    {
        unset($this->local[$key]);

        return $this->driver->forget($key);
    }

    /**
     * Remove all items from the cache.
     *
     * @return bool
     */
    public function flush(): bool
    {
        $this->local = [];

        return $this->driver->flush();
    }

    /**
     * Increment the value of a cache item.
     *
     * @param string $key
     * @param int $value
     * @return int|bool
     */
    public function increment(string $key, int $value = 1): int|bool
    {
        unset($this->local[$key]);

        return $this->driver->increment($key, $value);
    }

    /**
     * Decrement the value of a cache item.
     *
     * @param string $key
     * @param int $value
     * @return int|bool
     */
    public function decrement(string $key, int $value = 1): int|bool
    {
        unset($this->local[$key]);

        return $this->driver->decrement($key, $value);
    }
}
