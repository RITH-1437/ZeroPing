<?php

declare(strict_types=1);

namespace App\Http\Middleware;

abstract class Middleware
{
    /**
     * Handle an incoming request.
     *
     * Middleware should abort the request (throw an exception or redirect)
     * if the condition fails. A void return indicates the request should
     * proceed to the next middleware or controller.
     */
    abstract public function handle(): void;
}
