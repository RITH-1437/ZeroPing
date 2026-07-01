<?php

namespace App\Core;

class Auth
{
    public static function login(array $user): void
    {
        Session::set('user', $user);
    }

    public static function logout(): void
    {
        Session::destroy();
    }

    public static function user(): ?array
    {
        return Session::get('user');
    }

    public static function check(): bool
    {
        return Session::has('user');
    }

    public static function id(): ?int
    {
        return self::user()['id'] ?? null;
    }
}