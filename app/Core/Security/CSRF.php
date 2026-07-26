<?php

declare(strict_types=1);

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
        foreach ($tokens as $i => $stored) {
            if (hash_equals($stored, $token)) {
                unset($tokens[$i]);
                Session::set('_tokens', array_values($tokens));
                return true;
            }
        }
        return false;
    }
}
