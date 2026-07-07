<?php

namespace App\Core\Security\Middleware;

use App\Core\Http\Request;
use App\Core\Security\Signature;
use App\Core\Security\Exceptions\SecurityException;

class SignedUrlMiddleware
{
    public function handle(\Closure $next)
    {
        if (Signature::validate(Request::url())) {
            return $next(new Request());
        }

        throw new SecurityException('Invalid signature.');
    }
}
