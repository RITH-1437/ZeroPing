<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Core\Auth\AuthManager;
use App\Http\Response;

class GuestMiddleware extends Middleware
{
    public function handle(): void
    {
        if (AuthManager::check()) {
            Response::redirect('/');
        }
    }
}
