<?php

declare(strict_types=1);

namespace App\Core\Security\Middleware;

use App\Core\Http\Request;
use App\Core\Security\CSRF;
use App\Core\Security\Exceptions\SecurityException;

class VerifyCsrfToken
{
    protected array $except = [];

    /**
     * Handle the CSRF token verification.
     *
     * If the request is a read operation, is in the exception list,
     * or the CSRF tokens match, the request is allowed through.
     * Otherwise, a SecurityException is thrown.
     *
     * @throws SecurityException When the CSRF token does not match.
     */
    public function handle(): void
    {
        if (
            $this->isReading() ||
            $this->inExceptArray() ||
            $this->tokensMatch()
        ) {
            return;
        }

        throw new SecurityException('CSRF token mismatch.');
    }

    protected function isReading(): bool
    {
        return in_array(Request::method(), ['HEAD', 'GET', 'OPTIONS']);
    }

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

    protected function tokensMatch(): bool
    {
        $token = Request::input('_token') ?: Request::header('X-CSRF-TOKEN');

        return CSRF::check($token);
    }
}
