<?php

namespace App\Core\Cache\Drivers;

class ArrayCacheDriver implements CacheDriver
{
    protected array $storage = [];

    public function get(string $key, $default = null)
    {
        if (isset($this->storage[$key])) {
            $item = $this->storage[$key];

            if (time() >= $item['expire']) {
                $this->forget($key);
                return $default;
            }

            return $item['value'];
        }

        return $default;
    }

    public function put(string $key, $value, int $seconds): bool
    {
        $this->storage[$key] = [
            'value' => $value,
            'expire' => time() + $seconds,
        ];

        return true;
    }

    public function has(string $key): bool
    {
        return isset($this->storage[$key]);
    }

    public function forget(string $key): bool
    {
        unset($this->storage[$key]);

        return true;
    }

    public function flush(): bool
    {
        $this->storage = [];

        return true;
    }

    public function remember(string $key, int $seconds, callable $callback)
    {
        if (!is_null($value = $this->get($key))) {
            return $value;
        }

        $this->put($key, $value = $callback(), $seconds);

        return $value;
    }

    public function increment(string $key, int $value = 1): int|bool
    {
        if (!$this->has($key)) {
            return false;
        }

        $item          = $this->storage[$key];
        $item['value'] = $item['value'] + $value;

        $this->storage[$key] = $item;

        return $item['value'];
    }

    public function decrement(string $key, int $value = 1): int|bool
    {
        return $this->increment($key, $value * -1);
    }

    public function forever(string $key, $value): bool
    {
        return $this->put($key, $value, 999999999);
    }
}
