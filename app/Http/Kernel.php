<?php

declare(strict_types=1);

namespace App\Http;

use App\Core\Http\Kernel as BaseKernel;

/**
 * Application HTTP Kernel.
 *
 * Declares the global middleware stack, named middleware groups, and
 * execution priority for every incoming request. Route-level middleware
 * is declared directly on routes in {@code config/routes.php}.
 */
class Kernel extends BaseKernel
{
    /**
     * Global middleware executed before routing on every request.
     *
     * @var list<class-string|string>
     */
    protected array $middleware = [];

    /**
     * Named middleware groups referenced from route definitions.
     *
     * @var array<string, list<class-string|string>>
     */
    protected array $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\AuthMiddleware::class,
        ],
        'api' => [],
    ];

    /**
     * Middleware execution priority (lower index runs first).
     *
     * @var list<class-string|string>
     */
    protected array $middlewarePriority = [
        \App\Http\Middleware\GuestMiddleware::class,
        \App\Http\Middleware\AuthMiddleware::class,
    ];
}
