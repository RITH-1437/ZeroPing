<?php

declare(strict_types=1);

namespace App\Core\Security\Middleware;

use App\Core\Http\Request;
use App\Core\Security\CSRF;
use App\Core\Security\Exceptions\SecurityException;

/**
 * Middleware that verifies CSRF tokens on state-changing requests.
 *
 * Automatically skips verification for read-only methods (GET, HEAD, OPTIONS)
 * and any paths listed in the $except array.
 *
 * Token verification uses hash_equals() internally (via CSRF::check())
 * to prevent timing side-channel attacks.
 */
class VerifyCsrfToken
{
    /**
     * URI paths that should be excluded from CSRF verification.
     *
     * Supports wildcard matching via Request::is().
     *
     * @var array<int, string>
     */
    protected array $except = [];

    /**
     * Handle CSRF token verification for the current request.
     *
     * Allows the request through if it is a read operation, is in the
     * exception list, or contains a valid CSRF token. Otherwise, throws
     * a SecurityException.
     *
     * @return void
     *
     * @throws SecurityException When the CSRF token is missing or invalid.
     */
    public function handle(): void
    {
        if ($this->isReading() || $this->inExceptArray() || $this->tokensMatch()) {
            return;
        }

        throw new SecurityException('CSRF token mismatch.');
    }

    /**
     * Determine if the request uses a read-only HTTP method.
     *
     * @return bool True if the method is HEAD, GET, or OPTIONS.
     */
    protected function isReading(): bool
    {
        return in_array(Request::method(), ['HEAD', 'GET', 'OPTIONS'], true);
    }

    /**
     * Determine if the current request path is in the exception list.
     *
     * @return bool True if the path should skip CSRF verification.
     */
    protected function inExceptArray(): bool
    {
        foreach ($this->except as $except) {
            if ($except !== '/') {
                $except = trim($except, '/');
            }

            if (Request::is($except)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Verify that the request contains a valid CSRF token.
     *
     * Checks both the _token form field and the X-CSRF-TOKEN header,
     * using timing-safe comparison via hash_equals().
     *
     * @return bool True if the token is valid.
     */
    protected function tokensMatch(): bool
    {
        $token = Request::input('_token') ?: Request::header('X-CSRF-TOKEN');

        if ($token === null || $token === '') {
            return false;
        }

        return CSRF::check((string) $token);
    }
}
