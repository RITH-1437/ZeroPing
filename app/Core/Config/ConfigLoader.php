<?php

declare(strict_types=1);

namespace App\Core\Config;

/**
 * Loads configuration files from the config/ directory.
 *
 * Each PHP file in the config directory is expected to return an array.
 * The filename (without extension) becomes the top-level config key.
 * Certain infrastructure files (routes, constants, config.php) are excluded.
 */
class ConfigLoader
{
    /**
     * Files to exclude from configuration loading.
     *
     * @var array<int, string>
     */
    private const EXCLUDED_FILES = [
        'config.php',
        'constants.php',
        'routes.php',
    ];

    /**
     * Load all configuration files and return them as a nested array.
     *
     * @return array<string, mixed> Configuration keyed by filename (without extension).
     */
    public function load(): array
    {
        $configs = [];

        if (!defined('BASE_PATH')) {
            return $configs;
        }

        $configDir = BASE_PATH . '/config';

        if (!is_dir($configDir)) {
            return $configs;
        }

        $files = glob($configDir . '/*.php');

        if ($files === false) {
            return $configs;
        }

        foreach ($files as $file) {
            $name = basename($file);

            if (in_array($name, self::EXCLUDED_FILES, true)) {
                continue;
            }

            $key = pathinfo($name, PATHINFO_FILENAME);
            $configs[$key] = require $file;
        }

        return $configs;
    }
}
