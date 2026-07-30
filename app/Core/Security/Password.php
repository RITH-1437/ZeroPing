<?php

declare(strict_types=1);

namespace App\Core\Security;

/**
 * Password reset service facade.
 *
 * Provides a convenient static interface for obtaining a PasswordBroker
 * instance to handle password reset link sending and password resets.
 *
 * Usage:
 *   $status = Password::broker()->sendResetLink(['email' => $email]);
 */
class Password
{
    /** @var string Status constant: user not found. */
    public const INVALID_USER = 'passwords.user';

    /** @var string Status constant: reset link sent. */
    public const RESET_LINK_SENT = 'passwords.sent';

    /** @var string Status constant: password was reset. */
    public const PASSWORD_RESET = 'passwords.reset';

    /** @var string Status constant: invalid token. */
    public const INVALID_TOKEN = 'passwords.token';

    /**
     * Get a PasswordBroker instance.
     *
     * @return PasswordBroker The password broker for sending reset links and resetting passwords.
     */
    public static function broker(): PasswordBroker
    {
        return new PasswordBroker();
    }
}
