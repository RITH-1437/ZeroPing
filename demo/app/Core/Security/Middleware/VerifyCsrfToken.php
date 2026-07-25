<?php

namespace App\Core\Security\Middleware;

use App\Core\Http\Request;
use App\Core\Security\CSRF;
use App\Core\Security\Exceptions\SecurityException;

class VerifyCsrfToken
{
    protected array $except = [];

    public function handle(\Closure $next)
    {
        if (
            $this->isReading() ||
            $this->inExceptArray() ||
            $this->tokensMatch()
        ) {
            return $next(new Request());
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
