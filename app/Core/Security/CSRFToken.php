<?php

namespace App\Core\Security;

class CSRFToken
{
    public static function get(): string
    {
        return CSRF::generate();
    }

    public static function field(): string
    {
        return '<input type="hidden" name="_token" value="' . static::get() . '">';
    }
}
