<?php

declare(strict_types=1);

namespace App\Core\Session;

use App\Core\Auth\SessionGuard;

/**
 * Thin session wrapper providing both static and DI-friendly access.
 *
 * Delegates all operations to the underlying SessionGuard while maintaining
 * backward compatibility with static method calls. Can also be instantiated
 * and injected as a dependency via the SessionInterface contract.
 *
 * Static usage (backward-compatible):
 *   Session::set('key', 'value');
 *   Session::get('key');
 *
 * Instance usage via DI:
 *   $container->singleton(SessionInterface::class, Session::class);
 *   $session->put('key', 'value');
 *   $session->pull('key');
 */
class Session implements SessionInterface
{
    // ─── Static API (backward-compatible) ──────────────────────────────

    /**
     * Start the session.
     *
     * @return void
     */
    public static function start(): void
    {
        SessionGuard::start();
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
        SessionGuard::set($key, $value);
    }

    /**
     * Get a value from the session.
     *
     * @param string $key     The session key.
     * @param mixed  $default Default value if key doesn't exist.
     * @return mixed The session value, or the default.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return SessionGuard::get($key, $default);
    }

    /**
     * Determine if a key exists in the session.
     *
     * @param string $key The session key.
     * @return bool True if the key exists.
     */
    public static function has(string $key): bool
    {
        return SessionGuard::has($key);
    }

    /**
     * Remove a value from the session.
     *
     * @param string $key The session key.
     * @return void
     */
    public static function remove(string $key): void
    {
        SessionGuard::remove($key);
    }

    /**
     * Retrieve all session data (static).
     *
     * @return array<string, mixed> All session data.
     */
    public static function getAll(): array
    {
        return SessionGuard::all();
    }

    /**
     * Regenerate the session ID (static).
     *
     * @return void
     */
    public static function regenerateId(): void
    {
        SessionGuard::regenerate();
    }

    /**
     * Destroy the session (static).
     *
     * @return void
     */
    public static function destroy(): void
    {
        SessionGuard::destroy();
    }

    // ─── Instance API (DI-friendly, implements SessionInterface) ───────

    /**
     * {@inheritdoc}
     */
    public function startSession(): void
    {
        SessionGuard::start();
    }

    /**
     * {@inheritdoc}
     */
    public function put(string $key, mixed $value): void
    {
        SessionGuard::set($key, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function pull(string $key, mixed $default = null): mixed
    {
        return SessionGuard::get($key, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function exists(string $key): bool
    {
        return SessionGuard::has($key);
    }

    /**
     * {@inheritdoc}
     */
    public function forget(string $key): void
    {
        SessionGuard::remove($key);
    }

    /**
     * {@inheritdoc}
     */
    public function all(): array
    {
        return SessionGuard::all();
    }

    /**
     * {@inheritdoc}
     */
    public function regenerate(): void
    {
        SessionGuard::regenerate();
    }

    /**
     * {@inheritdoc}
     */
    public function flush(): void
    {
        SessionGuard::destroy();
    }
}
