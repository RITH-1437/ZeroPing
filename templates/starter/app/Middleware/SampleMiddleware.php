<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Core\Http\Response;
use App\Http\Request;

class SampleMiddleware extends Middleware
{
    public function handle(): void
    {
        if (empty(Request::header('X-ZeroPing'))) {
            Response::json(['error' => 'Forbidden'], 403);
        }
    }
}
