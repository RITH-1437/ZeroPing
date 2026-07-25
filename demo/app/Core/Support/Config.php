<?php

namespace App\Core\Support;

class Config
{
    protected static array $items = [];

    public static function get(string $key, $default = null)
    {
        if (empty(static::$items)) {
            static::load();
        }

        if (str_contains($key, '.')) {
            $parts = explode('.', $key);
            $value = static::$items;
            foreach ($parts as $part) {
                if (!is_array($value) || !array_key_exists($part, $value)) {
                    return $default;
                }
                $value = $value[$part];
            }
            return $value;
        }

        return static::$items[$key] ?? $default;
    }

    public static function all(): array
    {
        if (empty(static::$items)) {
            static::load();
        }

        return static::$items;
    }

    protected static function load(): void
    {
        if (!defined('BASE_PATH')) {
            return;
        }

        $configDir = BASE_PATH . '/config';
        $cacheFile = BASE_PATH . '/bootstrap/cache/config.php';

        // Share the compiled config cache produced by App::loadConfig /
        // config:cache so we don't re-glob the config directory on every boot.
        if (
            is_dir($configDir) && file_exists($cacheFile)
            && filemtime($cacheFile) >= self::configDirMtime($configDir)
        ) {
            $items = require $cacheFile;
            if (is_array($items)) {
                static::$items = $items;
                return;
            }
        }

        if (!is_dir($configDir)) {
            return;
        }

        $files = glob($configDir . '/*.php');
        if ($files === false) {
            return;
        }

        foreach ($files as $file) {
            $key = basename($file, '.php');
            static::$items[$key] = require $file;
        }
    }

    private static function configDirMtime(string $configDir): int
    {
        $files = glob($configDir . '/*.php') ?: [];
        $mtime = 0;
        foreach ($files as $file) {
            $mtime = max($mtime, filemtime($file));
        }
        return $mtime;
    }

    public static function setItems(array $items): void
    {
        static::$items = $items;
    }

    /**
     * Set a config value using "dot" notation, merging into the loaded items.
     *
     * Used by package service providers' mergeConfigFrom() so that merged
     * package defaults are readable through the global config() helper.
     */
    public static function set(string $key, $value): void
    {
        if (empty(static::$items)) {
            static::load();
        }

        $segments = explode('.', $key);
        $config =& static::$items;

        foreach ($segments as $segment) {
            if (!isset($config[$segment]) || !is_array($config[$segment])) {
                $config[$segment] = [];
            }

            $config =& $config[$segment];
        }

        $config = $value;
    }
}
