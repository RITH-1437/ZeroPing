<?php

namespace App\Http\Middleware;

use App\Auth\AuthManager;
use App\Http\Response;

class AuthMiddleware extends Middleware
{
    public function handle(): void
    {
        if (!AuthManager::check()) {
            Response::redirect('/login');
        }
    }
}