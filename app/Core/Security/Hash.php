<?php

namespace App\Core\Security;

class Hash
{
    /**
     * Hash a value using bcrypt.
     *
     * @param string $value
     * @return string
     */
    public static function make(string $value): string
    {
        return password_hash($value, PASSWORD_BCRYPT);
    }

    /**
     * Verify a plain-text value against a hashed value.
     *
     * @param string $value
     * @param string $hashedValue
     * @return bool
     */
    public static function check(string $value, string $hashedValue): bool
    {
        return password_verify($value, $hashedValue);
    }

    /**
     * Check if the given hash matches the current algorithm options.
     *
     * @param string $hashedValue
     * @return bool
     */
    public static function needsRehash(string $hashedValue): bool
    {
        return password_needs_rehash($hashedValue, PASSWORD_BCRYPT);
    }
}
