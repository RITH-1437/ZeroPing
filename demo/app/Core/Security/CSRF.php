<?php

namespace App\Core\Security;

use App\Core\Session\Session;

class CSRF
{
    public static function generate(): string
    {
        $token = bin2hex(random_bytes(32));
        $tokens = Session::get('_tokens', []);
        $tokens[] = $token;
        if (count($tokens) > 10) {
            $tokens = array_slice($tokens, -10);
        }
        Session::set('_tokens', $tokens);
        return $token;
    }

    public static function check(string $token): bool
    {
        $tokens = Session::get('_tokens', []);
        foreach ($tokens as $stored) {
            if (hash_equals($stored, $token)) {
                return true;
            }
        }
        return false;
    }
}
