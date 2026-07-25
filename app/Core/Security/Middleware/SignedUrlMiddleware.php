<?php

declare(strict_types=1);

namespace App\Core\Security\Middleware;

use App\Core\Http\Request;
use App\Core\Security\Signature;
use App\Core\Security\Exceptions\SecurityException;

class SignedUrlMiddleware
{
    /**
     * Handle signed URL validation.
     *
     * If the signature on the request URL is valid, the request is allowed
     * through. Otherwise, a SecurityException is thrown.
     *
     * @throws SecurityException When the URL signature is invalid.
     */
    public function handle(): void
    {
        if (Signature::validate(Request::url())) {
            return;
        }

        throw new SecurityException('Invalid signature.');
    }
}
