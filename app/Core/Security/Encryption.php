<?php

declare(strict_types=1);

namespace App\Core\Security;

use App\Core\Support\Config;

class Encryption
{
    /**
     * Encrypt a plain-text value using AES-256-GCM.
     *
     * GCM provides authenticated encryption (confidentiality + integrity),
     * preventing padding oracle attacks and tampering.
     *
     * @param string $value
     * @return string
     */
    public static function encrypt(string $value): string
    {
        $key = Config::get('security.key');
        $iv = random_bytes(12); // 96-bit IV is recommended for GCM
        $tag = '';

        $encrypted = openssl_encrypt(
            $value,
            'AES-256-GCM',
            $key,
            0,
            $iv,
            $tag
        );

        // Encode as base64(iv + tag + ciphertext) for safe transport
        return base64_encode($iv . $tag . $encrypted);
    }

    /**
     * Decrypt an AES-256-GCM encrypted value.
     *
     * The authentication tag is verified before the decrypted data is returned.
     *
     * @param string $value
     * @return string
     */
    public static function decrypt(string $value): string
    {
        $key = Config::get('security.key');

        $decoded = base64_decode($value, true);
        if ($decoded === false) {
            throw new \RuntimeException('Invalid base64-encoded ciphertext.');
        }

        // GCM format: iv (12 bytes) + tag (16 bytes) + ciphertext
        $iv = substr($decoded, 0, 12);
        $tag = substr($decoded, 12, 16);
        $encrypted = substr($decoded, 28);

        $result = openssl_decrypt(
            $encrypted,
            'AES-256-GCM',
            $key,
            0,
            $iv,
            $tag
        );

        if ($result === false) {
            throw new \RuntimeException(
                'Decryption failed: the ciphertext may have been tampered with '
                . 'or the key is incorrect.'
            );
        }

        return $result;
    }
}
