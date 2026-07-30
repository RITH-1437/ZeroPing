<?php

declare(strict_types=1);

namespace App\Core\Http;

use App\Core\Application\App;
use App\Core\Container\Container;
use App\Core\Http\ErrorRenderer;
use App\Core\Routing\Router;
use Throwable;

/**
 * HTTP Kernel — the heart of the request lifecycle.
 *
 * Runs global middleware (priority-ordered) then dispatches through the
 * router, which applies per-route middleware and middleware groups.
 * Exceptions bubble to a single renderer so error pages stay consistent.
 *
 * Applications extend {@see \App\Http\Kernel} to declare their global
 * middleware, middleware groups (e.g. "web", "api"), and priority ordering.
 */
class Kernel
{
    /**
     * Global middleware (short names or FQCNs) run on every request.
     *
     * @var list<class-string|string>
     */
    protected array $middleware = [];

    /**
     * Named middleware groups (e.g. "web" => [...]).
     * Routes may reference a group name in their middleware list.
     *
     * @var array<string, list<class-string|string>>
     */
    protected array $middlewareGroups = [];

    /**
     * Middleware priority — listed names run first.
     * Unlisted middleware run afterwards in their registration order.
     *
     * @var list<class-string|string>
     */
    protected array $middlewarePriority = [];

    /** @var App The application instance. */
    protected App $app;

    /**
     * HTTP status codes considered valid error codes for the error renderer.
     *
     * @var list<int>
     */
    protected array $knownErrorCodes = [400, 401, 403, 404, 405, 419, 422, 429, 500, 502, 503];

    public function __construct(App $app)
    {
        $this->app = $app;
    }

    /**
     * Handle the incoming HTTP request.
     *
     * Runs global middleware in priority order, then delegates to the
     * router for route matching and dispatch.
     */
    public function handle(): void
    {
        try {
            $this->bootstrap();

            foreach ($this->sortMiddleware($this->globalMiddleware()) as $name) {
                $this->callMiddleware($name);
            }

            Router::dispatch($this->app->basePath());
        } catch (Throwable $e) {
            $this->handleException($e);
        }
    }

    /**
     * Bootstrap hook — called at the start of handle().
     *
     * Override in subclasses to run additional setup before middleware.
     * The application is already booted by the front controller before
     * handle() is invoked.
     */
    protected function bootstrap(): void
    {
        //
    }

    /**
     * Return the global middleware list.
     *
     * @return list<class-string|string>
     */
    protected function globalMiddleware(): array
    {
        return $this->middleware;
    }

    /**
     * Instantiate and execute a single middleware.
     *
     * Uses the service container for resolution, enabling constructor
     * injection in middleware classes.
     */
    protected function callMiddleware(string $name): void
    {
        $class      = $this->resolveMiddlewareClass($name);
        $container  = App::container();
        $middleware = $container->make($class);

        $middleware->handle();
    }

    /**
     * Resolve a middleware name to its fully-qualified class name.
     *
     * Accepts a FQCN directly, or a short name which is expanded to
     * {@code App\Http\Middleware\{Name}Middleware}.
     *
     * @throws \RuntimeException When the middleware class cannot be found.
     */
    public function resolveMiddlewareClass(string $name): string
    {
        if (class_exists($name)) {
            return $name;
        }

        $class = 'App\\Http\\Middleware\\' . ucfirst($name) . 'Middleware';

        if (!class_exists($class)) {
            throw new \RuntimeException(
                "Middleware '{$name}' could not be resolved: class {$class} not found."
            );
        }

        return $class;
    }

    /**
     * Sort middleware by declared priority (lower index runs first).
     *
     * Middleware not listed in the priority array retain their original order
     * and run after all prioritized middleware.
     *
     * @param list<class-string|string> $names
     * @return list<class-string|string>
     */
    protected function sortMiddleware(array $names): array
    {
        $priority = array_flip($this->middlewarePriority);
        $sorted   = $names;

        usort(
            $sorted,
            static function (string $a, string $b) use ($priority): int {
                $pa = $priority[$a] ?? PHP_INT_MAX;
                $pb = $priority[$b] ?? PHP_INT_MAX;

                return $pa <=> $pb;
            }
        );

        return $sorted;
    }

    /**
     * Render an exception as an HTTP error response.
     *
     * Maps the exception code to an HTTP status when it matches a known
     * error code; otherwise defaults to 500 Internal Server Error.
     */
    protected function handleException(Throwable $e): void
    {
        $code = in_array((int) $e->getCode(), $this->knownErrorCodes, true)
            ? (int) $e->getCode()
            : 500;

        $frameworkPath = dirname(__DIR__, 3);

        ErrorRenderer::render($frameworkPath, $code, $e);
    }
}
