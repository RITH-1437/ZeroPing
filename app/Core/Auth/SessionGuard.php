<?php

declare(strict_types=1);

namespace App\Core\Auth;

use App\Core\Support\Config;

class SessionGuard
{
    private static bool $started = false;

    public static function start(): void
    {
        if (self::$started) {
            return;
        }

        if (PHP_SAPI === 'cli' && session_status() === PHP_SESSION_NONE) {
            $_SESSION ??= [];
            self::$started = true;
            return;
        }

        if (session_status() === PHP_SESSION_NONE) {
            $config = self::config();
            ini_set('session.use_strict_mode', '1');
            ini_set('session.use_only_cookies', '1');
            ini_set('session.use_trans_sid', '0');
            ini_set('session.cookie_httponly', '1');
            ini_set('session.cookie_secure', !empty($config['secure']) ? '1' : '0');
            ini_set('session.cookie_samesite', (string) ($config['samesite'] ?? 'Lax'));
            ini_set('session.cookie_lifetime', (string) max(0, (int) ($config['lifetime'] ?? 120) * 60));
            ini_set('session.cookie_path', (string) ($config['path'] ?? '/'));

            if (($config['domain'] ?? null) !== null) {
                ini_set('session.cookie_domain', (string) $config['domain']);
            }
            if (PHP_VERSION_ID < 80500) {
                ini_set('session.sid_length', '48');
                ini_set('session.sid_bits_per_character', '6');
            }

            session_start();
        }

        self::$started = true;
    }

    public static function regenerate(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
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

        if (session_status() !== PHP_SESSION_ACTIVE) {
            self::$started = false;
            return;
        }

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', [
                'expires' => time() - 42000,
                'path' => $params['path'],
                'domain' => $params['domain'],
                'secure' => $params['secure'],
                'httponly' => $params['httponly'],
                'samesite' => $params['samesite'],
            ]);
        }

        session_destroy();
        self::$started = false;
    }

    /** @return array<string, mixed> */
    private static function config(): array
    {
        try {
            $config = Config::get('session', []);
            return is_array($config) ? $config : [];
        } catch (\Throwable) {
            return [];
        }
    }
}
