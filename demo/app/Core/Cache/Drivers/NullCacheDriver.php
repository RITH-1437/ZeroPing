<?php

namespace App\Core\Cache\Drivers;

class NullCacheDriver implements CacheDriver
{
    public function get(string $key, $default = null)
    {
        return $default;
    }

    public function put(string $key, $value, int $seconds): bool
    {
        return true;
    }

    public function has(string $key): bool
    {
        return false;
    }

    public function forget(string $key): bool
    {
        return true;
    }

    public function flush(): bool
    {
        return true;
    }

    public function remember(string $key, int $seconds, callable $callback)
    {
        return $callback();
    }

    public function increment(string $key, int $value = 1): int|bool
    {
        return false;
    }

    public function decrement(string $key, int $value = 1): int|bool
    {
        return false;
    }

    public function forever(string $key, $value): bool
    {
        return true;
    }
}
