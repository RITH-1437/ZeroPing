<?php

namespace App\Core\Cache\Drivers;

interface CacheDriver
{
    public function get(string $key, $default = null);

    public function put(string $key, $value, int $seconds): bool;

    public function has(string $key): bool;

    public function forget(string $key): bool;

    public function flush(): bool;

    public function remember(string $key, int $seconds, callable $callback);

    public function increment(string $key, int $value = 1): int|bool;

    public function decrement(string $key, int $value = 1): int|bool;

    public function forever(string $key, $value): bool;
}
