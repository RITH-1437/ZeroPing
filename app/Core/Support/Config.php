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
        $files = glob(BASE_PATH . '/config/*.php');

        foreach ($files as $file) {
            $key = basename($file, '.php');
            static::$items[$key] = require $file;
        }
    }
}
