<?php

namespace App\Core\Auth;

class AuthManager
{
    public static function login(array $user): void
    {
        SessionGuard::set('user', $user);
    }

    public static function logout(): void
    {
        SessionGuard::destroy();
    }

    public static function user(): ?array
    {
        return SessionGuard::get('user');
    }

    public static function check(): bool
    {
        return SessionGuard::has('user');
    }

    public static function id(): ?int
    {
        return self::user()['id'] ?? null;
    }
}
