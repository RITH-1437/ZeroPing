<?php

namespace App\Core\Security;

use App\Core\Database\Database;
use App\Core\Mail\Mail;
use App\Core\Mail\Message;
use App\Core\Support\Config;
use App\Models\User;

class PasswordBroker
{
    public function sendResetLink(array $credentials): string
    {
        $user = $this->getUser($credentials);

        if (is_null($user)) {
            return 'passwords.user';
        }

        $token = $this->tokens()->create($user);

        $this->emailResetLink($user, $token);

        return 'passwords.sent';
    }

    public function reset(array $credentials, \Closure $callback)
    {
        $user = $this->validateReset($credentials);

        if (! $user instanceof User) {
            return $user;
        }

        $password = $credentials['password'];

        $callback($user, $password);

        $this->tokens()->delete($credentials['token']);

        return 'passwords.reset';
    }

    protected function getUser(array $credentials)
    {
        $user = User::where('email', $credentials['email'])->first();

        if ($user && ! $this->tokens()->recentlyCreatedToken($user)) {
            return $user;
        }
    }

    protected function tokens()
    {
        return new DatabaseTokenRepository(
            Database::connect(),
            Config::get('app.key'),
            'password_resets',
            60
        );
    }

    protected function emailResetLink($user, $token)
    {
        $view = 'auth.emails.password';

        return Mail::send($view, compact('token', 'user'), function (Message $message) use ($user, $token) {
            $message->to($user->email)->subject('Password Reset');
        });
    }

    protected function validateReset(array $credentials)
    {
        if (is_null($user = $this->getUser($credentials))) {
            return 'passwords.user';
        }

        if (! $this->tokens()->exists($user, $credentials['token'])) {
            return 'passwords.token';
        }

        return $user;
    }
}
