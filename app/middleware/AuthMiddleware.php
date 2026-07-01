<?php

class AuthMiddleware extends Middleware
{
    public function handle(): void
    {
        if (!Auth::check()) {
            Response::redirect('/login');
        }
    }
}