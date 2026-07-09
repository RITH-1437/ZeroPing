<?php

namespace App\Core\Security;

use App\Core\Session\Session;

class CSRFToken
{
    public static function get(): string
    {
        $tokens = Session::get('_tokens', []);
        if (empty($tokens)) {
            return CSRF::generate();
        }
        return end($tokens);
    }

    public static function regenerate(): string
    {
        return CSRF::generate();
    }

    public static function field(): string
    {
        return '<input type="hidden" name="_token" value="' . static::get() . '">';
    }
}
