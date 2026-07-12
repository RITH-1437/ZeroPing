<?php

namespace App\Core\Cache\Drivers;

use App\Core\Support\Log;

class FileCacheDriver implements CacheDriver
{
    protected string $path;

    public function __construct(array $config)
    {
        $this->path = $config['path'];
    }

    public function get(string $key, $default = null)
    {
        $file = $this->path . '/' . sha1($key);

        if (!file_exists($file)) {
            Log::info("Cache miss for key: {$key}");
            return $default;
        }

        $content = file_get_contents($file);
        $data = json_decode($content, true);

        if (!$data || !isset($data['expire'])) {
            return $default;
        }

        if (time() >= $data['expire']) {
            $this->forget($key);
            Log::info("Cache expired for key: {$key}");
            return $default;
        }

        Log::info("Cache hit for key: {$key}");
        return $data['value'];
    }

    public function put(string $key, $value, int $seconds): bool
    {
        $file = $this->path . '/' . sha1($key);

        $data = [
            'value' => $value,
            'expire' => time() + $seconds,
        ];

        return file_put_contents($file, json_encode($data)) !== false;
    }

    public function has(string $key): bool
    {
        $file = $this->path . '/' . sha1($key);

        return file_exists($file);
    }

    public function forget(string $key): bool
    {
        $file = $this->path . '/' . sha1($key);

        if (file_exists($file)) {
            return unlink($file);
        }

        return false;
    }

    public function flush(): bool
    {
        $files = glob($this->path . '/*');

        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }

        Log::info('Cache cleared.');
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

        $current = $this->get($key);
        $new = $current + $value;

        $this->put($key, $new, 0);

        return $new;
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
