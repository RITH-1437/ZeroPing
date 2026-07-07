<?php

namespace App\Core\Security;

use App\Core\Session\Session;

class CSRF
{
    public static function generate(): string
    {
        $token = bin2hex(random_bytes(32));
        Session::set('_token', $token);
        return $token;
    }

    public static function check(string $token): bool
    {
        $sessionToken = Session::get('_token');
        return hash_equals($sessionToken, $token);
    }
}
