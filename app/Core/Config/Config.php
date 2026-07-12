<?php

namespace App\Core\Config;

class Config
{
    protected static ConfigRepository $repository;

    public static function setRepository(
        ConfigRepository $repository
    ): void {
        self::$repository = $repository;
    }

    public static function get(
        string $key,
        mixed $default = null
    ): mixed {
        return self::$repository->get($key, $default);
    }

    public static function has(string $key): bool
    {
        return self::$repository->has($key);
    }
}
