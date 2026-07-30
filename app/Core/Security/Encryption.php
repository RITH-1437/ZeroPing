<?php

declare(strict_types=1);

namespace App\Core\Security;

use App\Core\Support\Config;
use RuntimeException;

/**
 * Authenticated encryption service using AES-256-GCM.
 *
 * Provides confidentiality and integrity via Galois/Counter Mode (GCM),
 * which combines encryption with a message authentication code (MAC).
 * This prevents padding oracle attacks, ciphertext tampering, and
 * chosen-ciphertext attacks.
 *
 * Key requirements:
 * - The encryption key must be exactly 32 bytes (256 bits).
 * - Keys should be generated with `random_bytes(32)` and stored base64-encoded.
 *
 * @since 1.0.0
 * @author Rin Nairith
 * @link https://zero-ping.duckdns.org/docs/security
 *
 * Ciphertext format: base64(iv[12] + tag[16] + ciphertext)
 */
class Encryption
{
    /** @var string The cipher algorithm. */
    private const CIPHER = 'aes-256-gcm';

    /** @var int IV length in bytes (96 bits recommended for GCM). */
    private const IV_LENGTH = 12;

    /** @var int Authentication tag length in bytes. */
    private const TAG_LENGTH = 16;

    /**
     * Encrypt a plaintext value using AES-256-GCM authenticated encryption.
     *
     * The output is a base64-encoded string containing the IV, authentication
     * tag, and ciphertext concatenated together.
     *
     * @param string $value The plaintext value to encrypt.
     * @return string The base64-encoded ciphertext with IV and tag.
     *
     * @throws RuntimeException If the encryption key is missing or encryption fails.
     */
    public static function encrypt(string $value): string
    {
        $key = self::getKey();
        $iv = random_bytes(self::IV_LENGTH);
        $tag = '';

        $encrypted = openssl_encrypt(
            $value,
            self::CIPHER,
            $key,
            OPENSSL_RAW_DATA,
            $iv,
            $tag,
            '',
            self::TAG_LENGTH
        );

        if ($encrypted === false) {
            throw new RuntimeException('Encryption failed: ' . openssl_error_string());
        }

        // Format: iv (12 bytes) + tag (16 bytes) + raw ciphertext
        return base64_encode($iv . $tag . $encrypted);
    }

    /**
     * Decrypt an AES-256-GCM encrypted value.
     *
     * Verifies the authentication tag before returning plaintext. If the
     * ciphertext has been tampered with or the key is incorrect, decryption
     * will fail with an exception.
     *
     * @param string $value The base64-encoded ciphertext to decrypt.
     * @return string The decrypted plaintext value.
     *
     * @throws RuntimeException If decryption fails due to tampering, invalid key, or malformed input.
     */
    public static function decrypt(string $value): string
    {
        $key = self::getKey();

        $decoded = base64_decode($value, true);

        if ($decoded === false) {
            throw new RuntimeException('Invalid base64-encoded ciphertext.');
        }

        $minimumLength = self::IV_LENGTH + self::TAG_LENGTH + 1;
        if (strlen($decoded) < $minimumLength) {
            throw new RuntimeException('Ciphertext is too short to be valid.');
        }

        // Extract components: iv (12 bytes) + tag (16 bytes) + ciphertext
        $iv = substr($decoded, 0, self::IV_LENGTH);
        $tag = substr($decoded, self::IV_LENGTH, self::TAG_LENGTH);
        $encrypted = substr($decoded, self::IV_LENGTH + self::TAG_LENGTH);

        $result = openssl_decrypt(
            $encrypted,
            self::CIPHER,
            $key,
            OPENSSL_RAW_DATA,
            $iv,
            $tag
        );

        if ($result === false) {
            throw new RuntimeException(
                'Decryption failed: the ciphertext may have been tampered with '
                . 'or the key is incorrect.'
            );
        }

        return $result;
    }

    /**
     * Encrypt a value and serialize it, supporting arbitrary PHP types.
     *
     * @param mixed $value The value to encrypt (will be serialized).
     * @return string The encrypted, base64-encoded string.
     *
     * @throws RuntimeException If encryption fails.
     */
    public static function encryptValue(mixed $value): string
    {
        return self::encrypt(serialize($value));
    }

    /**
     * Decrypt and unserialize an encrypted value.
     *
     * @param string $payload The encrypted payload.
     * @return mixed The original unserialized value.
     *
     * @throws RuntimeException If decryption fails.
     */
    public static function decryptValue(string $payload): mixed
    {
        $decrypted = self::decrypt($payload);

        $value = @unserialize($decrypted);

        if ($value === false && $decrypted !== serialize(false)) {
            throw new RuntimeException('Failed to unserialize decrypted data.');
        }

        return $value;
    }

    /**
     * Retrieve and validate the encryption key from configuration.
     *
     * @return string The raw encryption key bytes.
     *
     * @throws RuntimeException If the key is not configured or is invalid.
     */
    private static function getKey(): string
    {
        $key = Config::get('security.key');

        if ($key === null || $key === '') {
            throw new RuntimeException(
                'Encryption key is not set. Run "php zero key:generate" to create one.'
            );
        }

        // Support base64-encoded keys (prefixed with "base64:")
        if (str_starts_with($key, 'base64:')) {
            $decoded = base64_decode(substr($key, 7), true);
            if ($decoded === false) {
                throw new RuntimeException('The encryption key is not valid base64.');
            }
            return $decoded;
        }

        return $key;
    }
}
