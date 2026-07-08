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

        // Support dot-notation: "cache.default" → $items['cache']['default']
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
        $files = glob(BASE_PATH . '/config/*.php');

        foreach ($files as $file) {
            $key = basename($file, '.php');
            static::$items[$key] = require $file;
        }
    }
}
