<?php

namespace App\Core\Config;

use Exception;

class Env
{
    /**
     * Tracks which .env paths have already been loaded this process.
     *
     * @var array<string, bool>
     */
    private static array $loaded = [];

    public static function load(string $path): void
    {
        // Env loading is idempotent within a process; skip it entirely once
        // the same path has already been loaded (App already guards this, but
        // package code may call load() defensively). This also makes repeated
        // calls O(1) instead of re-parsing or re-reading the cache file.
        if (isset(self::$loaded[$path])) {
            return;
        }

        if (!file_exists($path)) {
            throw new Exception(".env file not found.");
        }

        self::$loaded[$path] = true;

        $cacheFile = dirname($path) . '/bootstrap/cache/env_' . md5($path) . '.php';

        // Serve from a compiled cache when it exists and is not stale.
        if (file_exists($cacheFile) && filemtime($cacheFile) >= filemtime($path)) {
            $items = require $cacheFile;
            if (is_array($items)) {
                foreach ($items as $key => $value) {
                    $_ENV[$key] = $value;
                }
                return;
            }
        }

        $lines = file(
            $path,
            FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES
        );

        $items = [];

        foreach ($lines as $line) {

            if (str_starts_with(trim($line), '#')) {
                continue;
            }

            [$key, $value] = array_pad(
                explode('=', $line, 2),
                2,
                ''
            );

            $key = trim($key);
            $value = trim($value);

            $items[$key] = $value;
            $_ENV[$key] = $value;
        }

        self::writeCache($cacheFile, $items);
    }

    /**
     * Persist parsed env vars to a PHP file so subsequent boots skip parsing.
     */
    private static function writeCache(string $cacheFile, array $items): void
    {
        $dir = dirname($cacheFile);

        if (!is_dir($dir)) {
            @mkdir($dir, 0777, true);
        }

        if (!is_writable($dir)) {
            return;
        }

        file_put_contents(
            $cacheFile,
            '<?php return ' . var_export($items, true) . ';'
        );
    }
}