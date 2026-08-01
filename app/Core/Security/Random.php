<?php

declare(strict_types=1);

namespace App\Core\Security;

/**
 * Cryptographically secure random value generator.
 *
 * All methods use random_bytes() as the entropy source, which is backed
 * by the operating system's CSPRNG (e.g., /dev/urandom on Linux,
 * CryptGenRandom on Windows).
 */
class Random
{
    /**
     * Generate a random hexadecimal string of the specified character length.
     *
     * @param int $length The desired length in hex characters (must be even, >= 2).
     * @return string The random hex string.
     *
     * @throws \InvalidArgumentException If length is less than 2 or odd.
     */
    public static function string(int $length = 32): string
    {
        if ($length < 2) {
            throw new \InvalidArgumentException('Length must be at least 2.');
        }

        // Each byte produces 2 hex characters
        $bytes = (int) ceil($length / 2);
        $hex = bin2hex(random_bytes(max(1, $bytes)));

        // Trim to exact length in case of odd request
        return substr($hex, 0, $length);
    }

    /**
     * Generate a random UUID v4 (RFC 4122 compliant).
     *
     * Format: xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx
     * where y is one of [8, 9, a, b].
     *
     * @return string The UUID v4 string.
     */
    public static function uuid(): string
    {
        $bytes = random_bytes(16);

        // Set version to 4 (0100xxxx)
        $bytes[6] = chr((ord($bytes[6]) & 0x0f) | 0x40);
        // Set variant to RFC 4122 (10xxxxxx)
        $bytes[8] = chr((ord($bytes[8]) & 0x3f) | 0x80);

        return sprintf(
            '%02x%02x%02x%02x-%02x%02x-%02x%02x-%02x%02x-%02x%02x%02x%02x%02x%02x',
            ord($bytes[0]),
            ord($bytes[1]),
            ord($bytes[2]),
            ord($bytes[3]),
            ord($bytes[4]),
            ord($bytes[5]),
            ord($bytes[6]),
            ord($bytes[7]),
            ord($bytes[8]),
            ord($bytes[9]),
            ord($bytes[10]),
            ord($bytes[11]),
            ord($bytes[12]),
            ord($bytes[13]),
            ord($bytes[14]),
            ord($bytes[15])
        );
    }

    /**
     * Generate a random token suitable for API keys or reset tokens.
     *
     * @param int $length The desired length in hex characters.
     * @return string The random token string.
     */
    public static function token(int $length = 60): string
    {
        return static::string($length);
    }

    /**
     * Generate a random integer within the specified range (inclusive).
     *
     * @param int $min The minimum value.
     * @param int $max The maximum value.
     * @return int A cryptographically secure random integer.
     *
     * @throws \InvalidArgumentException If min > max.
     */
    public static function int(int $min, int $max): int
    {
        if ($min > $max) {
            throw new \InvalidArgumentException('Minimum must not exceed maximum.');
        }

        return random_int($min, $max);
    }

    /**
     * Generate random bytes.
     *
     * @param int $length The number of bytes to generate.
     * @return string The raw random bytes.
     */
    public static function bytes(int $length): string
    {
        return random_bytes(max(1, $length));
    }
}
