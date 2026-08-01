<?php

declare(strict_types=1);

namespace App\Core\Security;

/**
 * Password hashing utility using the best available algorithm.
 *
 * Prefers PASSWORD_ARGON2ID (memory-hard, resistant to GPU/ASIC attacks)
 * when the argon2id extension is available, falling back to PASSWORD_BCRYPT.
 *
 * Argon2id combines Argon2i (side-channel resistant) and Argon2d (GPU resistant)
 * and is recommended by OWASP for password storage.
 *
 * @since 1.0.0
 * @author Rin Nairith
 * @link https://zero-ping.duckdns.org/docs/security
 */
class Hash
{
    /**
     * Hash a plaintext value using the strongest available algorithm.
     *
     * Uses PASSWORD_ARGON2ID when available (PHP 7.3+ with libargon2),
     * falling back to PASSWORD_BCRYPT with a cost factor of 12.
     *
     * @param string               $value   The plaintext value to hash.
     * @param array<string, mixed> $options Algorithm-specific options
     *                                       (e.g., 'memory_cost', 'time_cost', 'threads' for Argon2).
     * @return string The hashed value.
     */
    public static function make(string $value, array $options = []): string
    {
        $algorithm = self::preferredAlgorithm();

        if ($algorithm === PASSWORD_ARGON2ID) {
            $defaults = [
                'memory_cost' => PASSWORD_ARGON2_DEFAULT_MEMORY_COST,
                'time_cost'   => PASSWORD_ARGON2_DEFAULT_TIME_COST,
                'threads'     => PASSWORD_ARGON2_DEFAULT_THREADS,
            ];
            $options = array_merge($defaults, $options);
        } else {
            // PASSWORD_BCRYPT
            $defaults = ['cost' => 12];
            $options = array_merge($defaults, $options);
        }

        $hash = password_hash($value, $algorithm, $options);

        return $hash;
    }

    /**
     * Verify a plaintext value against a hashed value.
     *
     * This method is safe against timing attacks as password_verify()
     * uses a constant-time comparison internally.
     *
     * @param string $value       The plaintext value to verify.
     * @param string $hashedValue The stored hash to check against.
     * @return bool True if the value matches the hash.
     */
    public static function check(string $value, string $hashedValue): bool
    {
        if ($hashedValue === '') {
            return false;
        }

        return password_verify($value, $hashedValue);
    }

    /**
     * Determine if a hash needs to be rehashed due to algorithm or option changes.
     *
     * Use this after a successful password_verify() to transparently upgrade
     * hashes when the user logs in.
     *
     * @param string               $hashedValue The hash to check.
     * @param array<string, mixed> $options     Algorithm-specific options to check against.
     * @return bool True if the hash should be regenerated.
     */
    public static function needsRehash(string $hashedValue, array $options = []): bool
    {
        $algorithm = self::preferredAlgorithm();

        if ($algorithm === PASSWORD_ARGON2ID) {
            $defaults = [
                'memory_cost' => PASSWORD_ARGON2_DEFAULT_MEMORY_COST,
                'time_cost'   => PASSWORD_ARGON2_DEFAULT_TIME_COST,
                'threads'     => PASSWORD_ARGON2_DEFAULT_THREADS,
            ];
            $options = array_merge($defaults, $options);
        } else {
            $defaults = ['cost' => 12];
            $options = array_merge($defaults, $options);
        }

        return password_needs_rehash($hashedValue, $algorithm, $options);
    }

    /**
     * Get information about a hashed value.
     *
     * @param string $hashedValue The hash to inspect.
     * @return array<string, mixed> Hash info.
     */
    public static function info(string $hashedValue): array
    {
        return password_get_info($hashedValue);
    }

    /**
     * Determine the preferred hashing algorithm.
     *
     * Returns PASSWORD_ARGON2ID if available, otherwise PASSWORD_BCRYPT.
     *
     * @return string The algorithm constant.
     */
    private static function preferredAlgorithm(): string
    {
        if (defined('PASSWORD_ARGON2ID')) {
            return PASSWORD_ARGON2ID;
        }

        return PASSWORD_BCRYPT;
    }
}
