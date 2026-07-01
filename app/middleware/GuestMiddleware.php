<?php

class GuestMiddleware extends Middleware
{
    public function handle(): void
    {
        if (Auth::check()) {
            Response::redirect('/');
        }
    }
}