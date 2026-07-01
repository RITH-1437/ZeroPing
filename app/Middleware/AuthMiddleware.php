<?php

namespace App\Middleware;

use App\Core\Auth;
use App\Core\Middleware;
use App\Core\Response;

class AuthMiddleware extends Middleware
{
    public function handle(): void
    {
        if (!Auth::check()) {
            Response::redirect('/login');
        }
    }
}