<?php

namespace App\Core\Cache\Drivers;

use App\Core\Support\Log;

class FileCacheDriver implements CacheDriver
{
    protected string $path;

    /**
     * Create a new file cache driver instance.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->path = $config['path'];
    }

    /**
     * Ensure the cache directory exists, creating it recursively if needed.
     */
    protected function ensureDirectory(): void
    {
        if (!is_dir($this->path)) {
            @mkdir($this->path, 0777, true);
        }
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
        $this->ensureDirectory();

        $file = $this->path . '/' . sha1($key);

        $data = [
            'value' => $value,
            'expire' => time() + $seconds,
        ];

        return file_put_contents($file, json_encode($data)) !== false;
    }

    /**
     * Determine if an item exists in the cache.
     *
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        $file = $this->path . '/' . sha1($key);

        return file_exists($file);
    }

    /**
     * Remove an item from the cache.
     *
     * @param string $key
     * @return bool
     */
    public function forget(string $key): bool
    {
        $file = $this->path . '/' . sha1($key);

        if (file_exists($file)) {
            return unlink($file);
        }

        return false;
    }

    /**
     * Remove all items from the cache.
     *
     * @return bool
     */
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
        if (!is_null($value = $this->get($key))) {
            return $value;
        }

        $this->put($key, $value = $callback(), $seconds);

        return $value;
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
        if (!$this->has($key)) {
            return false;
        }

        $current = $this->get($key);
        $new = $current + $value;

        // Preserve the item's remaining TTL rather than expiring it.
        $file   = $this->path . '/' . sha1($key);
        $expire = 999999999;

        if (file_exists($file)) {
            $data = json_decode(file_get_contents($file), true);
            if (isset($data['expire'])) {
                $expire = $data['expire'];
            }
        }

        $this->put($key, $new, max(0, $expire - time()));

        return $new;
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
        return $this->increment($key, $value * -1);
    }

    /**
     * Store an item in the cache indefinitely.
     *
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    public function forever(string $key, $value): bool
    {
        return $this->put($key, $value, 999999999);
    }
}
