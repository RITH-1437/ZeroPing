<?php

namespace App\Core\Security;

class Token
{
    public static function create(User $user)
    {
        return app('auth.password.tokens')->create($user);
    }

    public static function exists(User $user, $token)
    {
        return app('auth.password.tokens')->exists($user, $token);
    }
}
