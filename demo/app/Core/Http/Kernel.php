<?php

namespace App\Core\Http;

use App\Core\Application\App;
use App\Core\Routing\Router;
use Throwable;

/**
 * HTTP kernel — the heart of the request lifecycle.
 *
 * Runs global middleware (priority ordered) and then dispatches through
 * the router, which applies per-route middleware and middleware groups.
 * Exceptions bubble to a single renderer so error pages stay consistent.
 *
 * Apps extend App\Http\Kernel to declare their global middleware,
 * middleware groups (e.g. "web", "api") and priority.
 */
class Kernel
{
    /**
     * Global middleware (short names or FQCNs) run on every request.
     *
     * @var array<int, class-string|string>
     */
    protected array $middleware = [];

    /**
     * Named middleware groups (e.g. "web" => [...]). Routes may
     * reference a group name in their middleware list.
     *
     * @var array<string, array<int, class-string|string>>
     */
    protected array $middlewareGroups = [];

    /**
     * Middleware priority — listed names run first. Unlisted middleware
     * run afterwards in their registration order.
     *
     * @var array<int, class-string|string>
     */
    protected array $middlewarePriority = [];

    protected App $app;

    /**
     * @param App $app
     */
    public function __construct(App $app)
    {
        $this->app = $app;
    }

    /**
     * Handle an incoming request.
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
     * Hook for app bootstrap. The app is already booted by the
     * front controller (bootstrap/app.php) before handle() runs.
     */
    protected function bootstrap(): void
    {
        //
    }

    /**
     * Global middleware, merging any the app registers elsewhere.
     *
     * @return array<int, class-string|string>
     */
    protected function globalMiddleware(): array
    {
        return $this->middleware;
    }

    protected function callMiddleware(string $name): void
    {
        $class = $this->resolveMiddlewareClass($name);

        (new $class())->handle();
    }

    /**
     * Resolve a middleware name to a class.
     *
     * Accepts a FQCN directly, or a short name under
     * App\Http\Middleware\{Name}Middleware.
     *
     * @param string $name
     * @return string
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
     * Sort middleware by priority (lower runs first; unlisted keep order).
     *
     * @param array<int, class-string|string> $names
     * @return array<int, class-string|string>
     */
    protected function sortMiddleware(array $names): array
    {
        $priority = array_flip($this->middlewarePriority);
        $sorted  = $names;

        usort(
            $sorted,
            static function ($a, $b) use ($priority): int {
                $pa = $priority[$a] ?? PHP_INT_MAX;
                $pb = $priority[$b] ?? PHP_INT_MAX;

                return $pa <=> $pb;
            }
        );

        return $sorted;
    }

    protected function handleException(Throwable $e): void
    {
        $code = in_array($e->getCode(), [403, 404, 419], true) ? $e->getCode() : 500;

        $frameworkPath = dirname(__DIR__, 3);

        Router::renderError($frameworkPath, $code, $e);
    }
}
