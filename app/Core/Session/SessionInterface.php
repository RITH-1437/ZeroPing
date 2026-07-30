<?php

declare(strict_types=1);

namespace App\Core\Session;

/**
 * Contract for session management implementations.
 *
 * Provides an instance-based API for dependency injection consumers.
 * The concrete Session class additionally exposes static methods for
 * backward compatibility with existing code.
 */
interface SessionInterface
{
    /**
     * Start the session.
     *
     * @return void
     */
    public function startSession(): void;

    /**
     * Set a value in the session.
     *
     * @param string $key   The session key.
     * @param mixed  $value The value to store.
     * @return void
     */
    public function put(string $key, mixed $value): void;

    /**
     * Get a value from the session.
     *
     * @param string $key     The session key.
     * @param mixed  $default Default value if key doesn't exist.
     * @return mixed The session value, or the default.
     */
    public function pull(string $key, mixed $default = null): mixed;

    /**
     * Determine if a key exists in the session.
     *
     * @param string $key The session key.
     * @return bool True if the key exists.
     */
    public function exists(string $key): bool;

    /**
     * Remove a value from the session.
     *
     * @param string $key The session key.
     * @return void
     */
    public function forget(string $key): void;

    /**
     * Retrieve all session data.
     *
     * @return array<string, mixed> All session data.
     */
    public function all(): array;

    /**
     * Regenerate the session ID.
     *
     * @return void
     */
    public function regenerate(): void;

    /**
     * Destroy the session.
     *
     * @return void
     */
    public function flush(): void;
}
