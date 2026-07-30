<?php

declare(strict_types=1);

namespace App\Core\Config;

/**
 * Static facade for the configuration repository.
 *
 * Provides convenient static access to the underlying ConfigRepository.
 * All methods delegate to the repository instance set during application bootstrap.
 *
 * @see ConfigRepository
 */
class Config
{
    /**
     * The underlying configuration repository instance.
     */
    protected static ConfigRepository $repository;

    /**
     * Set the configuration repository instance.
     *
     * Called during application bootstrap after configuration files are loaded.
     *
     * @param ConfigRepository $repository The loaded repository.
     *
     * @return void
     */
    public static function setRepository(ConfigRepository $repository): void
    {
        self::$repository = $repository;
    }

    /**
     * Retrieve a configuration value using dot notation.
     *
     * @param string $key     The dot-notated key (e.g., "app.name").
     * @param mixed  $default The default if the key does not exist.
     *
     * @return mixed
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return self::$repository->get($key, $default);
    }

    /**
     * Determine if a configuration key exists.
     *
     * @param string $key The dot-notated key.
     *
     * @return bool
     */
    public static function has(string $key): bool
    {
        return self::$repository->has($key);
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
        self::$repository->setValue($key, $value);
    }

    /**
     * Get the underlying configuration repository instance.
     *
     * @return ConfigRepository
     */
    public static function getRepository(): ConfigRepository
    {
        return self::$repository;
    }
}
