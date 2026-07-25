<?php

declare(strict_types=1);

namespace App\Core\Support;

use App\Core\Config\Config as ConfigFacade;

class Config
{
    protected static array $items = [];

    /**
     * Get the underlying config repository, if it has been set up.
     *
     * When the app is fully bootstrapped, App\Core\Config\Config holds
     * a ConfigRepository loaded by App::loadConfig(). We delegate to
     * that single source of truth so that config reads are consistent
     * regardless of which Config facade is used.
     *
     * Falls back to the static array loaded from files when the
     * ConfigRepository is not available (e.g. before bootstrap).
     */
    protected static function getRepository(): ?\App\Core\Config\ConfigRepository
    {
        if (method_exists(ConfigFacade::class, 'getRepository')) {
            $repo = ConfigFacade::getRepository();
            if ($repo !== null) {
                return $repo;
            }
        }

        return null;
    }

    public static function get(string $key, $default = null)
    {
        $repo = static::getRepository();
        if ($repo !== null) {
            return $repo->get($key, $default);
        }

        // Bootstrap fallback: load from files if not yet cached
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
        $repo = static::getRepository();
        if ($repo !== null) {
            return $repo->all();
        }

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
        $repo = static::getRepository();
        if ($repo !== null) {
            $repo->setValue($key, $value);
            return;
        }

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
