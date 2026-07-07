<?php

namespace App\Core\Cache;

use App\Core\Cache\Drivers\CacheDriver;

class CacheRepository
{
    protected CacheDriver $driver;

    public function __construct(CacheDriver $driver)
    {
        $this->driver = $driver;
    }

    public function get(string $key, $default = null)
    {
        return $this->driver->get($key, $default);
    }

    public function put(string $key, $value, int $seconds): bool
    {
        return $this->driver->put($key, $value, $seconds);
    }

    public function remember(string $key, int $seconds, callable $callback)
    {
        return $this->driver->remember($key, $seconds, $callback);
    }

    public function rememberForever(string $key, callable $callback)
    {
        return $this->driver->forever($key, $callback());
    }

    public function has(string $key): bool
    {
        return $this->driver->has($key);
    }

    public function forget(string $key): bool
    {
        return $this->driver->forget($key);
    }

    public function flush(): bool
    {
        return $this->driver->flush();
    }

    public function increment(string $key, int $value = 1): int|bool
    {
        return $this->driver->increment($key, $value);
    }

    public function decrement(string $key, int $value = 1): int|bool
    {
        return $this->driver->decrement($key, $value);
    }
}
