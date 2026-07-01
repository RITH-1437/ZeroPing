<?php

namespace App\Core;
class Flash
{
    public static function set(string $type, string $message): void
    {
        Session::set('flash', [
            'type' => $type,
            'message' => $message
        ]);
    }

    public static function get(): ?array
    {
        $flash = Session::get('flash');

        Session::remove('flash');

        return $flash;
    }

    public static function has(): bool
    {
        return Session::has('flash');
    }

    public static function success(string $message): void
    {
        self::set('success', $message);
    }

    public static function error(string $message): void
    {
        self::set('error', $message);
    }

    public static function warning(string $message): void
    {
        self::set('warning', $message);
    }

    public static function info(string $message): void
    {
        self::set('info', $message);
    }
}