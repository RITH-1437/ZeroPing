<?php

namespace App\Core\Security;

use App\Core\Support\Config;

class Encryption
{
    public static function encrypt(string $value): string
    {
        $key = Config::get('security.key');
        $iv = random_bytes(16);
        $encrypted = openssl_encrypt($value, 'AES-256-CBC', $key, 0, $iv);
        return base64_encode($encrypted . '::' . $iv);
    }

    public static function decrypt(string $value): string
    {
        $key = Config::get('security.key');
        [$encrypted, $iv] = explode('::', base64_decode($value), 2);
        return openssl_decrypt($encrypted, 'AES-256-CBC', $key, 0, $iv);
    }
}
