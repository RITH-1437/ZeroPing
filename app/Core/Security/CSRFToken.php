<?php

declare(strict_types=1);

namespace App\Core\Security;

use App\Core\Session\Session;

/**
 * CSRF token accessor providing convenient retrieval and HTML field generation.
 *
 * Works alongside the CSRF class to provide a read-focused API for templates
 * and form builders.
 */
class CSRFToken
{
    /** @var string Session key for token storage. */
    private const SESSION_KEY = '_tokens';

    /**
     * Get the current CSRF token, generating one if none exists.
     *
     * Returns the most recently generated token from the session, or
     * creates a new one if no tokens are available.
     *
     * @return string The current CSRF token.
     */
    public static function get(): string
    {
        $session = new Session();
        $tokens = $session->get(self::SESSION_KEY, []);

        if (empty($tokens) || !is_array($tokens)) {
            return CSRF::generate();
        }

        return (string) end($tokens);
    }

    /**
     * Generate a fresh CSRF token, discarding any previous context.
     *
     * @return string The newly generated token.
     */
    public static function regenerate(): string
    {
        return CSRF::generate();
    }

    /**
     * Generate an HTML hidden input field containing the CSRF token.
     *
     * The token value is properly escaped to prevent XSS injection.
     *
     * @return string The HTML hidden input element.
     */
    public static function field(): string
    {
        $token = htmlspecialchars(static::get(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        return '<input type="hidden" name="_token" value="' . $token . '">';
    }

    /**
     * Get the CSRF token as a meta tag for JavaScript AJAX requests.
     *
     * @return string The HTML meta element containing the CSRF token.
     */
    public static function meta(): string
    {
        $token = htmlspecialchars(static::get(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        return '<meta name="csrf-token" content="' . $token . '">';
    }
}
