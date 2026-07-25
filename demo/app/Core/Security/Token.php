<?php

namespace App\Core\Security;

use App\Models\User;

class Token
{
    public static function create(User $user): string
    {
        return app(DatabaseTokenRepository::class)->create($user);
    }

    public static function exists(User $user, string $token): bool
    {
        return app(DatabaseTokenRepository::class)->exists($user, $token);
    }
}
