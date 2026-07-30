<?php

declare(strict_types=1);

namespace App\Core\Auth;

use App\Core\Support\Config;

/**
 * Session guard responsible for low-level session lifecycle management.
 *
 * Handles session start, configuration, regeneration, and destruction with
 * secure defaults (strict mode, HTTP-only cookies, SameSite policy).
 */
class SessionGuard
{
    /** @var bool Whether the session has been started by this guard. */
    private static bool $started = false;

    /**
     * Start the session if it has not already been started.
     *
     * Configures secure session settings from the application's session config
     * before calling session_start(). In CLI mode, initializes $_SESSION as an
     * in-memory array.
     *
     * @return void
     */
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

    /**
     * Regenerate the session ID to prevent session fixation attacks.
     *
     * Only regenerates if a session is currently active.
     *
     * @return void
     */
    public static function regenerate(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
    }

    /**
     * Set a value in the session.
     *
     * @param string $key   The session key.
     * @param mixed  $value The value to store.
     * @return void
     */
    public static function set(string $key, mixed $value): void
    {
        self::start();
        $_SESSION[$key] = $value;
    }

    /**
     * Get a value from the session.
     *
     * @param string $key     The session key to retrieve.
     * @param mixed  $default The default value if key does not exist.
     * @return mixed The session value, or the default.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        self::start();
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Determine if a key exists in the session.
     *
     * @param string $key The session key to check.
     * @return bool True if the key exists.
     */
    public static function has(string $key): bool
    {
        self::start();
        return isset($_SESSION[$key]);
    }

    /**
     * Remove a value from the session.
     *
     * @param string $key The session key to remove.
     * @return void
     */
    public static function remove(string $key): void
    {
        self::start();
        unset($_SESSION[$key]);
    }

    /**
     * Retrieve all session data.
     *
     * @return array<string, mixed> All session data.
     */
    public static function all(): array
    {
        self::start();
        return $_SESSION ?? [];
    }

    /**
     * Destroy the current session completely.
     *
     * Clears session data, removes the session cookie, and destroys
     * the session on the server.
     *
     * @return void
     */
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
                'expires'  => time() - 42000,
                'path'     => $params['path'],
                'domain'   => $params['domain'],
                'secure'   => $params['secure'],
                'httponly'  => $params['httponly'],
                'samesite' => $params['samesite'],
            ]);
        }

        session_destroy();
        self::$started = false;
    }

    /**
     * Retrieve session configuration from the application config.
     *
     * @return array<string, mixed> The session configuration array.
     */
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
