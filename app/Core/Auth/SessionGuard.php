<?php

namespace App\Core\Auth;

class SessionGuard
{
    /**
     * Tracks whether a session has been started in this process so that
     * repeated get/set/has calls don't re-invoke session_status() each time.
     */
    private static bool $started = false;

    public static function start(): void
    {
        if (self::$started) {
            return;
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        self::$started = true;
    }

    public static function set(string $key, mixed $value): void
    {
        self::start();

        $_SESSION[$key] = $value;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        self::start();

        return $_SESSION[$key] ?? $default;
    }

    public static function has(string $key): bool
    {
        self::start();

        return isset($_SESSION[$key]);
    }

    public static function remove(string $key): void
    {
        self::start();

        unset($_SESSION[$key]);
    }

    public static function destroy(): void
    {
        self::start();

        $_SESSION = [];

        session_destroy();

        self::$started = false;

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
    }
}