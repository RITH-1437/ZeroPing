<?php

namespace App\Core\Session;
use App\Core\Auth\SessionGuard;

class Flash
{
    public static function set(string $type, string $message): void
    {
        SessionGuard::set('flash', [
            'type' => $type,
            'message' => $message
        ]);
    }

    public static function get(): ?array
    {
        $flash = SessionGuard::get('flash');

        SessionGuard::remove('flash');

        return $flash;
    }

    public static function has(): bool
    {
        return SessionGuard::has('flash');
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