<?php

declare(strict_types=1);

namespace App\Core\Support;

use App\Core\Config\Config as ConfigFacade;
use App\Core\Config\ConfigRepository;

/**
 * Support-layer configuration accessor.
 *
 * Exists for backward compatibility and early-bootstrap scenarios where the
 * main Config facade may not yet have a repository attached. Once bootstrapped,
 * all calls delegate to the canonical ConfigRepository to avoid duplication.
 *
 * @see ConfigFacade
 * @see ConfigRepository
 */
class Config
{
    /**
     * Fallback items loaded from config files before bootstrap completes.
     *
     * @var array<string, mixed>
     */
    protected static array $items = [];

    /**
     * Retrieve a configuration value using dot notation.
     *
     * @param string $key     The dot-notated key (e.g., "app.name").
     * @param mixed  $default The default value if the key does not exist.
     *
     * @return mixed
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $repo = static::getRepository();

        if ($repo !== null) {
            return $repo->get($key, $default);
        }

        if (empty(static::$items)) {
            static::load();
        }

        return static::resolveKey($key, static::$items, $default);
    }

    /**
     * Return all configuration items as a nested array.
     *
     * @return array<string, mixed>
     */
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

    /**
     * Set a configuration value using dot notation.
     *
     * @param string $key   The dot-notated key.
     * @param mixed  $value The value to set.
     *
     * @return void
     */
    public static function set(string $key, mixed $value): void
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
        $config = &static::$items;

        foreach ($segments as $segment) {
            if (!isset($config[$segment]) || !is_array($config[$segment])) {
                $config[$segment] = [];
            }
            $config = &$config[$segment];
        }

        $config = $value;
    }

    /**
     * Replace all fallback items (useful for testing).
     *
     * @param array<string, mixed> $items The items to set.
     *
     * @return void
     */
    public static function setItems(array $items): void
    {
        static::$items = $items;
    }

    /**
     * Attempt to get the ConfigRepository from the core Config facade.
     *
     * @return ConfigRepository|null The repository or null if not initialized.
     */
    protected static function getRepository(): ?ConfigRepository
    {
        try {
            return ConfigFacade::getRepository();
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * Resolve a dot-notated key against a nested array.
     *
     * @param string              $key     The dot-notated key path.
     * @param array<string,mixed> $items   The array to search.
     * @param mixed               $default The default if not found.
     *
     * @return mixed
     */
    protected static function resolveKey(string $key, array $items, mixed $default = null): mixed
    {
        $segments = explode('.', $key);
        $value = $items;

        foreach ($segments as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }
            $value = $value[$segment];
        }

        return $value;
    }

    /**
     * Load config files from disk as a bootstrap fallback.
     *
     * @return void
     */
    protected static function load(): void
    {
        if (!defined('BASE_PATH')) {
            return;
        }

        $configDir = BASE_PATH . '/config';
        $cacheFile = BASE_PATH . '/bootstrap/cache/config.php';

        if (
            is_dir($configDir)
            && file_exists($cacheFile)
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

    /**
     * Get the most recent modification time of files in the config directory.
     *
     * @param string $configDir The config directory path.
     *
     * @return int Unix timestamp of the most recently modified file.
     */
    private static function configDirMtime(string $configDir): int
    {
        $files = glob($configDir . '/*.php') ?: [];
        $mtime = 0;

        foreach ($files as $file) {
            $mtime = max($mtime, (int) filemtime($file));
        }

        return $mtime;
    }
}
