<?php

declare(strict_types=1);

namespace App\Core\Cache\Drivers;

class RedisCacheDriver implements CacheDriver
{
    protected \Redis $redis;
    protected string $prefix;

    /**
     * @param array<string, mixed> $config
     */
    public function __construct(array $config)
    {
        if (!extension_loaded('redis')) {
            throw new \RuntimeException('The Redis cache driver requires the ext-redis PHP extension.');
        }

        $this->redis = new \Redis();
        $this->redis->connect(
            $config['host'] ?? '127.0.0.1',
            (int)($config['port'] ?? 6379),
        );

        if (!empty($config['password'])) {
            $this->redis->auth($config['password']);
        }

        $this->redis->select((int)($config['database'] ?? 0));
        $this->prefix = (string)($config['prefix'] ?? 'zeroping_cache:');
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $value = $this->redis->get($this->prefix . $key);
        if ($value === false) {
            return $default;
        }
        $decoded = unserialize($value);
        return $decoded === false ? $default : $decoded;
    }

    public function put(string $key, mixed $value, int $seconds = 3600): bool
    {
        return (bool)$this->redis->setex(
            $this->prefix . $key,
            max(1, $seconds),
            serialize($value)
        );
    }

    public function forever(string $key, mixed $value): bool
    {
        return (bool)$this->redis->set($this->prefix . $key, serialize($value));
    }

    public function has(string $key): bool
    {
        return (bool)$this->redis->exists($this->prefix . $key);
    }

    public function forget(string $key): bool
    {
        return (bool)$this->redis->del($this->prefix . $key);
    }

    public function flush(): bool
    {
        $keys = $this->redis->keys($this->prefix . '*');
        if (!empty($keys)) {
            $this->redis->del($keys);
        }
        return true;
    }

    public function remember(string $key, int $seconds, callable $callback): mixed
    {
        $cached = $this->get($key);
        if ($cached !== null) {
            return $cached;
        }
        $value = $callback();
        $this->put($key, $value, $seconds);
        return $value;
    }

    public function rememberForever(string $key, callable $callback): mixed
    {
        $cached = $this->get($key);
        if ($cached !== null) {
            return $cached;
        }
        $value = $callback();
        $this->forever($key, $value);
        return $value;
    }

    public function increment(string $key, int $value = 1): int|false
    {
        if (!$this->has($key)) {
            $this->put($key, 0, 86400);
        }
        return $this->redis->incrBy($this->prefix . $key, $value);
    }

    public function decrement(string $key, int $value = 1): int|false
    {
        return $this->redis->decrBy($this->prefix . $key, $value);
    }
}