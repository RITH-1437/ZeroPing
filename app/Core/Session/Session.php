<?php

namespace App\Core\Session;

class Session
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function set(string $key, $value): void
    {
        static::start();
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, $default = null)
    {
        static::start();
        return $_SESSION[$key] ?? $default;
    }

    public static function has(string $key): bool
    {
        static::start();
        return isset($_SESSION[$key]);
    }

    public static function remove(string $key): void
    {
        static::start();
        unset($_SESSION[$key]);
    }
}
