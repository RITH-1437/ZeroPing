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
     * @var array<string, mixed>
     */
    protected array $local = [];

    public function __construct(CacheDriver $driver)
    {
        $this->driver = $driver;
    }

    public function get(string $key, $default = null)
    {
        if (array_key_exists($key, $this->local)) {
            return $this->local[$key];
        }

        $value = $this->driver->get($key, $default);

        $this->local[$key] = $value;

        return $value;
    }

    public function put(string $key, $value, int $seconds): bool
    {
        $this->local[$key] = $value;

        return $this->driver->put($key, $value, $seconds);
    }

    public function remember(string $key, int $seconds, callable $callback)
    {
        if (array_key_exists($key, $this->local)) {
            return $this->local[$key];
        }

        $value = $this->driver->remember($key, $seconds, $callback);

        $this->local[$key] = $value;

        return $value;
    }

    public function rememberForever(string $key, callable $callback)
    {
        if (array_key_exists($key, $this->local)) {
            return $this->local[$key];
        }

        $value = $this->driver->forever($key, $callback());

        $this->local[$key] = $value;

        return $value;
    }

    public function forever(string $key, mixed $value): bool
    {
        // Store with a very long TTL (10 years)
        $this->local[$key] = $value;

        return $this->driver->put($key, $value, 315360000);
    }

    public function has(string $key): bool
    {
        if (array_key_exists($key, $this->local)) {
            return $this->local[$key] !== null;
        }

        return $this->driver->has($key);
    }

    public function forget(string $key): bool
    {
        unset($this->local[$key]);

        return $this->driver->forget($key);
    }

    public function flush(): bool
    {
        $this->local = [];

        return $this->driver->flush();
    }

    public function increment(string $key, int $value = 1): int|bool
    {
        unset($this->local[$key]);

        return $this->driver->increment($key, $value);
    }

    public function decrement(string $key, int $value = 1): int|bool
    {
        unset($this->local[$key]);

        return $this->driver->decrement($key, $value);
    }
}
