<?php

declare(strict_types=1);

namespace App\Core\Security;

use App\Core\Session\Session;

/**
 * CSRF (Cross-Site Request Forgery) protection manager.
 *
 * Generates and validates CSRF tokens using cryptographically secure random
 * bytes and timing-safe comparison via hash_equals() to prevent timing attacks.
 *
 * Maintains a rolling window of valid tokens (max 10) to support AJAX
 * requests and multi-tab browsing scenarios.
 */
class CSRF
{
    /** @var int Maximum number of tokens to retain in the session. */
    private const MAX_TOKENS = 10;

    /** @var int Token length in bytes (generates 64 hex characters). */
    private const TOKEN_BYTES = 32;

    /** @var string Session key for token storage. */
    private const SESSION_KEY = '_tokens';

    /**
     * Generate a new CSRF token and store it in the session.
     *
     * Uses cryptographically secure random bytes to produce a hex-encoded
     * token. Older tokens are pruned to maintain the rolling window limit.
     *
     * @return string The generated CSRF token (64 hex characters).
     */
    public static function generate(): string
    {
        $token = bin2hex(random_bytes(self::TOKEN_BYTES));

        $tokens = self::getTokens();
        $tokens[] = $token;

        if (count($tokens) > self::MAX_TOKENS) {
            $tokens = array_slice($tokens, -self::MAX_TOKENS);
        }

        self::setTokens($tokens);

        return $token;
    }

    /**
     * Validate a CSRF token against stored session tokens.
     *
     * Uses hash_equals() for timing-safe comparison to prevent timing
     * side-channel attacks. On successful validation, the consumed token
     * is removed from the session.
     *
     * @param string $token The token to validate.
     * @return bool True if the token is valid, false otherwise.
     */
    public static function check(string $token): bool
    {
        if ($token === '') {
            return false;
        }

        $tokens = self::getTokens();

        foreach ($tokens as $i => $stored) {
            if (hash_equals($stored, $token)) {
                unset($tokens[$i]);
                self::setTokens(array_values($tokens));
                return true;
            }
        }

        return false;
    }

    /**
     * Retrieve all stored tokens from the session.
     *
     * @return array<int, string> List of stored tokens.
     */
    private static function getTokens(): array
    {
        $session = new Session();
        $tokens = $session->get(self::SESSION_KEY, []);

        return is_array($tokens) ? $tokens : [];
    }

    /**
     * Store tokens in the session.
     *
     * @param array<int, string> $tokens Tokens to store.
     * @return void
     */
    private static function setTokens(array $tokens): void
    {
        $session = new Session();
        $session->set(self::SESSION_KEY, $tokens);
    }
}
