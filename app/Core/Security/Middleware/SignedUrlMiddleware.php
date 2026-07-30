<?php

declare(strict_types=1);

namespace App\Core\Security\Middleware;

use App\Core\Http\Request;
use App\Core\Security\Signature;
use App\Core\Security\Exceptions\SecurityException;

/**
 * Middleware that validates signed URL signatures.
 *
 * Ensures that the current request URL has a valid HMAC signature,
 * preventing URL tampering for sensitive actions like email verification
 * or unsubscribe links.
 *
 * Uses hash_equals() internally (via Signature::validate()) for
 * timing-safe signature comparison.
 */
class SignedUrlMiddleware
{
    /**
     * Handle signed URL validation for the current request.
     *
     * If the URL signature is valid, the request proceeds normally.
     * Otherwise, a SecurityException is thrown.
     *
     * @return void
     *
     * @throws SecurityException When the URL signature is invalid or missing.
     */
    public function handle(): void
    {
        if (Signature::validate(Request::url())) {
            return;
        }

        throw new SecurityException('Invalid signature.');
    }
}
