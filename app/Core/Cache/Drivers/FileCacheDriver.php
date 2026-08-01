<?php

declare(strict_types=1);

namespace App\Core\Cache\Drivers;

/**
 * File-based cache driver.
 *
 * Stores each cache entry as a JSON file on disk. File writes are performed
 * atomically (write to a temp file, then rename) to prevent corruption from
 * concurrent processes or crashes mid-write.
 */
class FileCacheDriver implements CacheDriver
{
    /**
     * The directory path where cache files are stored.
     */
    protected string $path;

    /**
     * Create a new file cache driver instance.
     *
     * @param array{path: string} $config Driver configuration containing the storage path.
     */
    public function __construct(array $config)
    {
        $this->path = rtrim($config['path'], '/\\');
    }

    /**
     * Ensure the cache directory exists, creating it recursively if needed.
     *
     * @return void
     */
    protected function ensureDirectory(): void
    {
        if (!is_dir($this->path)) {
            @mkdir($this->path, 0755, true);
        }
    }

    /**
     * Get the full file path for a given cache key.
     *
     * @param string $key The cache key.
     *
     * @return string The absolute path to the cache file.
     */
    protected function filePath(string $key): string
    {
        return $this->path . DIRECTORY_SEPARATOR . sha1($key);
    }

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
        $file = $this->filePath($key);

        if (!file_exists($file)) {
            return $default;
        }

        $content = file_get_contents($file);

        if ($content === false) {
            return $default;
        }

        $data = json_decode($content, true);

        if (!is_array($data) || !isset($data['expire'], $data['value'])) {
            return $default;
        }

        if (time() >= $data['expire']) {
            $this->forget($key);
            return $default;
        }

        return $data['value'];
    }

    /**
     * Store an item in the cache using an atomic write.
     *
     * The value is first written to a temporary file, then renamed to its
     * final location. This prevents readers from seeing partially-written data.
     *
     * @param string $key     The cache key.
     * @param mixed  $value   The value to store (must be JSON-serializable).
     * @param int    $seconds Time-to-live in seconds.
     *
     * @return bool True on success, false on failure.
     */
    public function put(string $key, mixed $value, int $seconds): bool
    {
        $this->ensureDirectory();

        $file = $this->filePath($key);

        $data = json_encode([
            'value'  => $value,
            'expire' => time() + $seconds,
        ]);

        if ($data === false) {
            return false;
        }

        return $this->atomicWrite($file, $data);
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
        return $this->get($key, $this) !== $this;
    }

    /**
     * Remove an item from the cache.
     *
     * @param string $key The cache key to remove.
     *
     * @return bool True on success, false if the file did not exist.
     */
    public function forget(string $key): bool
    {
        $file = $this->filePath($key);

        if (file_exists($file)) {
            return @unlink($file);
        }

        return false;
    }

    /**
     * Remove all items from the cache directory.
     *
     * @return bool True on success.
     */
    public function flush(): bool
    {
        $files = glob($this->path . DIRECTORY_SEPARATOR . '*');

        if ($files === false) {
            return true;
        }

        foreach ($files as $file) {
            if (is_file($file)) {
                @unlink($file);
            }
        }

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
        $file = $this->filePath($key);

        if (!file_exists($file)) {
            return false;
        }

        $content = file_get_contents($file);

        if ($content === false) {
            return false;
        }

        $data = json_decode($content, true);

        if (!is_array($data) || !isset($data['expire'], $data['value'])) {
            return false;
        }

        if (time() >= $data['expire']) {
            $this->forget($key);
            return false;
        }

        $newValue = (int) $data['value'] + $value;
        $remainingTtl = max(0, $data['expire'] - time());

        $this->put($key, $newValue, (int) $remainingTtl);

        return $newValue;
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
     * @return bool True on success, false on failure.
     */
    public function forever(string $key, mixed $value): bool
    {
        return $this->put($key, $value, 315_360_000);
    }

    /**
     * Write content to a file atomically using a temporary file and rename.
     *
     * On systems where rename() is atomic (most POSIX systems and NTFS),
     * this guarantees that concurrent readers will never see a partial file.
     *
     * @param string $file    The destination file path.
     * @param string $content The content to write.
     *
     * @return bool True on success, false on failure.
     */
    protected function atomicWrite(string $file, string $content): bool
    {
        $tempFile = $file . '.' . uniqid('', true) . '.tmp';

        if (file_put_contents($tempFile, $content) === false) {
            return false;
        }

        // On Windows, rename fails if the target exists; remove it first.
        if (DIRECTORY_SEPARATOR === '\\' && file_exists($file)) {
            @unlink($file);
        }

        if (!@rename($tempFile, $file)) {
            @unlink($tempFile);
            return false;
        }

        return true;
    }
}
