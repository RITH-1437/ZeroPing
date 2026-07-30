<?php

declare(strict_types=1);

namespace App\Core\Security;

use App\Models\User;

/**
 * Token service for managing password reset and verification tokens.
 *
 * Acts as a facade over the DatabaseTokenRepository to provide
 * a clean, static API for token creation and validation.
 */
class Token
{
    /**
     * Create a new token for the given user.
     *
     * Any existing tokens for the user will be deleted before creating the new one.
     *
     * @param User $user The user to create a token for.
     * @return string The raw token value (to be sent to the user).
     */
    public static function create(User $user): string
    {
        return app(DatabaseTokenRepository::class)->create($user);
    }

    /**
     * Determine if a valid token exists for the given user.
     *
     * Validates both the token value (via password_verify) and that
     * the token has not expired.
     *
     * @param User   $user  The user to check.
     * @param string $token The raw token value to verify.
     * @return bool True if a valid, non-expired token exists.
     */
    public static function exists(User $user, string $token): bool
    {
        return app(DatabaseTokenRepository::class)->exists($user, $token);
    }

    /**
     * Delete all tokens for a given user.
     *
     * @param User $user The user whose tokens should be removed.
     * @return void
     */
    public static function delete(User $user): void
    {
        app(DatabaseTokenRepository::class)->delete($user);
    }
}
