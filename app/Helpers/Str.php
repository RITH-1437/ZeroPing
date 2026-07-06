<?php

namespace App\Helpers;

class Str
{
    public static function classBasename(string $class): string
    {
        return basename(str_replace('\\', '/', $class));
    }

    public static function snake(string $value): string
    {
        return strtolower(
            preg_replace('/(?<!^)[A-Z]/', '_$0', $value)
        );
    }

    public static function studly(string $value): string
    {
        return str_replace(
            ' ',
            '',
            ucwords(str_replace(['-', '_'], ' ', $value))
        );
    }

    public static function camel(string $value): string
    {
        return lcfirst(self::studly($value));
    }
}