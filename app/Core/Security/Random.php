<?php

namespace App\Core\Security;

class Random
{
    public static function string(int $length = 32): string
    {
        return bin2hex(random_bytes($length / 2));
    }

    public static function uuid(): string
    {
        $bytes = random_bytes(16);
        $bytes[6] = chr((ord($bytes[6]) & 0x0f) | 0x40);
        $bytes[8] = chr((ord($bytes[8]) & 0x3f) | 0x80);

        return sprintf(
            '%02x%02x%02x%02x-%02x%02x-%02x%02x-%02x%02x-%02x%02x%02x%02x%02x%02x',
            ord($bytes[0]), ord($bytes[1]), ord($bytes[2]), ord($bytes[3]),
            ord($bytes[4]), ord($bytes[5]),
            ord($bytes[6]), ord($bytes[7]),
            ord($bytes[8]), ord($bytes[9]),
            ord($bytes[10]), ord($bytes[11]), ord($bytes[12]),
            ord($bytes[13]), ord($bytes[14]), ord($bytes[15])
        );
    }

    public static function token(int $length = 60): string
    {
        return static::string($length);
    }
}
