<?php
declare(strict_types=1);

namespace App\Core\Auth;

use App\Models\User;

class AuthManager
{
    public static function login(array $user): void
    {
        SessionGuard::regenerate();
        SessionGuard::set('user_id', $user['id'] ?? null);
    }

    public static function logout(): void
    {
        SessionGuard::destroy();
    }

    public static function user(): ?array
    {
        $id = SessionGuard::get('user_id');
        if ($id === null) {
            return null;
        }
        return User::find((int) $id);
    }

    public static function check(): bool
    {
        return SessionGuard::has('user_id');
    }

    public static function id(): ?int
    {
        return SessionGuard::get('user_id');
    }
}
