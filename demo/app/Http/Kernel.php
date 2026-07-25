<?php

namespace App\Http;

use App\Core\Http\Kernel as BaseKernel;

/**
 * Application HTTP kernel.
 *
 * Declares the global middleware, middleware groups and priority
 * used by every request. Route-level middleware is declared on
 * the individual routes in config/routes.php.
 */
class Kernel extends BaseKernel
{
    /**
     * Global middleware run before the router on every request.
     *
     * @var array<int, class-string|string>
     */
    protected array $middleware = [];

    /**
     * Middleware groups referenced by name from routes.
     *
     * @var array<string, array<int, class-string|string>>
     */
    protected array $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\AuthMiddleware::class,
        ],
        'api' => [],
    ];

    /**
     * Middleware priority (lower runs first).
     *
     * @var array<int, class-string|string>
     */
    protected array $middlewarePriority = [
        \App\Http\Middleware\GuestMiddleware::class,
        \App\Http\Middleware\AuthMiddleware::class,
    ];
}
