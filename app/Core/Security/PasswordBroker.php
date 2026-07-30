<?php

declare(strict_types=1);

namespace App\Core\Security;

use App\Core\Database\Database;
use App\Core\Mail\Mail;
use App\Core\Mail\Message;
use App\Core\Support\Config;
use App\Models\User;
use Closure;

/**
 * Password broker responsible for sending reset links and processing resets.
 *
 * Coordinates between the User model, DatabaseTokenRepository, and Mail
 * system to provide a complete password reset flow.
 */
class PasswordBroker
{
    /**
     * Send a password reset link to the user matching the given credentials.
     *
     * @param array{email: string} $credentials The user credentials (must include 'email').
     * @return string A status constant indicating the result (passwords.sent or passwords.user).
     */
    public function sendResetLink(array $credentials): string
    {
        $user = $this->getUser($credentials);

        if ($user === null) {
            return Password::INVALID_USER;
        }

        $token = $this->tokens()->create($user);

        $this->emailResetLink($user, $token);

        return Password::RESET_LINK_SENT;
    }

    /**
     * Reset the password for the user matching the given credentials.
     *
     * Validates the user exists and the token is valid before invoking
     * the callback to perform the actual password update.
     *
     * @param array{email: string, password: string, token: string} $credentials The reset credentials.
     * @param Closure(User, string): void $callback Callback that receives the User and new password.
     * @return string A status constant indicating the result.
     */
    public function reset(array $credentials, Closure $callback): string
    {
        $user = $this->validateReset($credentials);

        if (!$user instanceof User) {
            return $user;
        }

        $password = $credentials['password'];

        $callback($user, $password);

        $this->tokens()->delete($user);

        return Password::PASSWORD_RESET;
    }

    /**
     * Retrieve the user by credentials, ensuring no recent token exists.
     *
     * @param array{email: string} $credentials The user credentials.
     * @return User|null The user, or null if not found or rate-limited.
     */
    protected function getUser(array $credentials): ?User
    {
        $user = User::where('email', $credentials['email'])->first();

        if ($user === null) {
            return null;
        }

        if ($this->tokens()->recentlyCreatedToken($user)) {
            return null;
        }

        return $user;
    }

    /**
     * Create a new DatabaseTokenRepository instance.
     *
     * @return DatabaseTokenRepository The token repository.
     */
    protected function tokens(): DatabaseTokenRepository
    {
        return new DatabaseTokenRepository(
            Database::connect(),
            (string) Config::get('app.key'),
            'password_resets',
            60
        );
    }

    /**
     * Send the password reset email to the user.
     *
     * @param User   $user  The user to email.
     * @param string $token The raw reset token.
     * @return void
     */
    protected function emailResetLink(User $user, string $token): void
    {
        $view = 'auth.emails.password';

        Mail::send($view, compact('token', 'user'), function (Message $message) use ($user): void {
            $message->to($user->email)->subject('Password Reset');
        });
    }

    /**
     * Validate the reset request by checking user existence and token validity.
     *
     * @param array{email: string, token: string} $credentials The reset credentials.
     * @return User|string The user if valid, or a status string if validation fails.
     */
    protected function validateReset(array $credentials): User|string
    {
        $user = User::where('email', $credentials['email'])->first();

        if ($user === null) {
            return Password::INVALID_USER;
        }

        if (!$this->tokens()->exists($user, $credentials['token'])) {
            return Password::INVALID_TOKEN;
        }

        return $user;
    }
}
