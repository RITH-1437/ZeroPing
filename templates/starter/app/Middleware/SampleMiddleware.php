<?php

namespace App\Http\Middleware;

use App\Http\Request;

/**
 * SampleMiddleware — an illustrative HTTP middleware.
 *
 * Middleware receives the incoming Request and may inspect or transform it
 * before the controller runs. Extend App\Http\Middleware\Middleware and
 * implement handle().
 */
class SampleMiddleware extends Middleware
{
    /**
     * Handle the incoming request.
     */
    public function handle(): void
    {
        // Example: block requests without a custom header.
        if (empty(Request::header('X-ZeroPing'))) {
            http_response_code(403);
            echo 'Forbidden';
            exit;
        }
    }
}
