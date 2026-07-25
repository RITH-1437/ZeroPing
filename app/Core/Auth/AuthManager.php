<?php
declare(strict_types=1);

namespace App\Core\Auth;

class AuthManager
{
    public static function login(array $user): void
    {
        SessionGuard::regenerate();
        SessionGuard::set('user', $user);
        SessionGuard::set('user_id', $user['id'] ?? null);
    }

    public static function logout(): void
    {
        SessionGuard::destroy();
    }

    public static function user(): ?array
    {
        $user = SessionGuard::get('user');
        if ($user === null) {
            return null;
        }
        return $user;
    }

    public static function check(): bool
    {
        return SessionGuard::has('user');
    }

    public static function id(): ?int
    {
        return SessionGuard::get('user_id');
    }
}
