<?php

declare(strict_types=1);

namespace App\Core\Auth;

/**
 * Authentication manager responsible for login, logout, and session-based user retrieval.
 *
 * Provides a static facade over the SessionGuard to manage authenticated user state.
 */
class AuthManager
{
    /**
     * Log a user in by storing their data in the session.
     *
     * Regenerates the session ID to prevent session fixation attacks.
     *
     * @param array<string, mixed> $user The user data array (must include 'id' key).
     * @return void
     */
    public static function login(array $user): void
    {
        SessionGuard::regenerate();
        SessionGuard::set('user', $user);
        SessionGuard::set('user_id', $user['id'] ?? null);
    }

    /**
     * Log the current user out by destroying the session.
     *
     * @return void
     */
    public static function logout(): void
    {
        SessionGuard::destroy();
    }

    /**
     * Retrieve the currently authenticated user data.
     *
     * @return array<string, mixed>|null The user data array, or null if not authenticated.
     */
    public static function user(): ?array
    {
        $user = SessionGuard::get('user');

        if ($user === null) {
            return null;
        }

        return $user;
    }

    /**
     * Determine if the current session has an authenticated user.
     *
     * @return bool True if a user is authenticated.
     */
    public static function check(): bool
    {
        return SessionGuard::has('user');
    }

    /**
     * Retrieve the currently authenticated user's ID.
     *
     * @return int|null The user's ID, or null if not authenticated.
     */
    public static function id(): ?int
    {
        return SessionGuard::get('user_id');
    }

    /**
     * Determine if the current session does NOT have an authenticated user.
     *
     * @return bool True if no user is authenticated.
     */
    public static function guest(): bool
    {
        return !static::check();
    }
}
