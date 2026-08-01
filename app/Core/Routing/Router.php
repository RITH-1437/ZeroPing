<?php

declare(strict_types=1);

namespace App\Core\Routing;

use App\Core\Application\App;
use App\Core\Http\ErrorRenderer;

/**
 * Static route registry and dispatcher.
 *
 * Provides a fluent, static API for defining routes (GET, POST, PUT,
 * PATCH, DELETE, OPTIONS, ANY), grouping them with shared prefix and
 * middleware, matching incoming requests, and dispatching to the
 * appropriate controller action or closure.
 *
 * ## Dispatch Flow (Router::dispatch)
 *
 * 1. **Resolve URI** — normalizes REQUEST_URI (strips query string, trailing slash).
 * 2. **Load routes** — requires config/routes.php which registers routes via
 *    static method calls (get/post/etc.).
 * 3. **Match route** — first attempts an O(1) exact match from the route map;
 *    if no hit, iterates dynamic routes using compiled+cached regex patterns.
 * 4. **Execute middleware** — expands named middleware groups, resolves short
 *    names to App\Http\Middleware\*Middleware FQCNs (cached), instantiates and
 *    calls handle() on each sequentially.
 * 5. **Call action** — invokes the matched Closure or resolves the controller
 *    from the DI Container and calls the specified method with URI parameters.
 * 6. **Send response** — Response objects are sent directly; objects with
 *    toResponse() are converted; strings are echoed.
 * 7. **Error handling** — any uncaught Throwable is caught and rendered via
 *    ErrorRenderer with the appropriate HTTP status code.
 *
 * @since 1.0.0
 * @author Rin Nairith
 * @link https://zero-ping.duckdns.org/docs/cli
 */
class Router
{
    /** @var array<string, array<string, Route>> */
    private static array $routes = [];

    /**
     * Static (literal) routes indexed by "METHOD|uri" for O(1) lookup.
     *
     * @var array<string, Route>
     */
    private static array $staticRoutes = [];

    /**
     * Dynamic routes (containing parameters) indexed by method.
     *
     * @var array<string, array<string, Route>>
     */
    private static array $dynamicRoutes = [];

    /** @var Route|null The currently matched route. */
    private static ?Route $current = null;

    /** @var string Active group prefix. */
    private static string $prefix = '';

    /** @var array<int, string> Active group middleware. */
    private static array $groupMiddleware = [];

    /**
     * Named middleware groups (e.g. "web", "api").
     *
     * @var array<string, array<int, class-string|string>>
     */
    private static array $middlewareGroups = [];

    /**
     * Compiled regex patterns, cached per "METHOD|uri".
     *
     * @var array<string, string>
     */
    private static array $compiledPatterns = [];

/**
      * Resolved middleware class names, cached per short name.
      *
      * @var array<string, class-string>
      */
      private static array $middlewareClasses = [];

    /**
     * Lazy name => uri map for route().
     *
     * @var array<string, string>|null
     */
    private static ?array $nameMap = null;

    /**
     * Expanded middleware cache to avoid re-expanding the same lists.
     *
     * @var array<string, array<int, class-string|string>>
     */
    private static array $expandedMiddlewareCache = [];

    // ──────────────────────────────────────────────────────────────────
    // Route Registration
    // ──────────────────────────────────────────────────────────────────

    /**
     * Register a GET route.
     *
     * @param string                                   $uri
     * @param array{0: class-string, 1: string}|\Closure $action
     * @param array<int, string>                        $middleware
     * @return Route
     */
    public static function get(string $uri, array|\Closure $action, array $middleware = []): Route
    {
        return self::addRoute('GET', $uri, $action, $middleware);
    }

    /**
     * Register a POST route.
     *
     * @param string                                   $uri
     * @param array{0: class-string, 1: string}|\Closure $action
     * @param array<int, string>                        $middleware
     * @return Route
     */
    public static function post(string $uri, array|\Closure $action, array $middleware = []): Route
    {
        return self::addRoute('POST', $uri, $action, $middleware);
    }

    /**
     * Register a PUT route.
     *
     * @param string                                   $uri
     * @param array{0: class-string, 1: string}|\Closure $action
     * @param array<int, string>                        $middleware
     * @return Route
     */
    public static function put(string $uri, array|\Closure $action, array $middleware = []): Route
    {
        return self::addRoute('PUT', $uri, $action, $middleware);
    }

    /**
     * Register a PATCH route.
     *
     * @param string                                   $uri
     * @param array{0: class-string, 1: string}|\Closure $action
     * @param array<int, string>                        $middleware
     * @return Route
     */
    public static function patch(string $uri, array|\Closure $action, array $middleware = []): Route
    {
        return self::addRoute('PATCH', $uri, $action, $middleware);
    }

    /**
     * Register a DELETE route.
     *
     * @param string                                   $uri
     * @param array{0: class-string, 1: string}|\Closure $action
     * @param array<int, string>                        $middleware
     * @return Route
     */
    public static function delete(string $uri, array|\Closure $action, array $middleware = []): Route
    {
        return self::addRoute('DELETE', $uri, $action, $middleware);
    }

    /**
     * Register an OPTIONS route.
     *
     * @param string                                   $uri
     * @param array{0: class-string, 1: string}|\Closure $action
     * @param array<int, string>                        $middleware
     * @return Route
     */
    public static function options(string $uri, array|\Closure $action, array $middleware = []): Route
    {
        return self::addRoute('OPTIONS', $uri, $action, $middleware);
    }

    /**
     * Register a route for ALL HTTP methods.
     *
     * @param string                                   $uri
     * @param array{0: class-string, 1: string}|\Closure $action
     * @param array<int, string>                        $middleware
     * @return Route
     */
    public static function any(string $uri, array|\Closure $action, array $middleware = []): Route
    {
        $route = null;

        foreach (['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'] as $method) {
            $route = self::addRoute($method, $uri, $action, $middleware);
        }

        /** @var Route $route */
        return $route;
    }

    /**
     * Register a route that responds to multiple HTTP methods.
     *
     * @param array<int, string>                        $methods
     * @param string                                   $uri
     * @param array{0: class-string, 1: string}|\Closure $action
     * @param array<int, string>                        $middleware
     * @return Route
     */
    public static function match(array $methods, string $uri, array|\Closure $action, array $middleware = []): Route
    {
        $route = null;

        foreach ($methods as $method) {
            $route = self::addRoute(strtoupper($method), $uri, $action, $middleware);
        }

        /** @var Route $route */
        return $route;
    }

    // ──────────────────────────────────────────────────────────────────
    // Grouping
    // ──────────────────────────────────────────────────────────────────

    /**
     * Register routes under a URI prefix.
     *
     * @param string   $prefix
     * @param callable $callback
     */
    public static function prefix(string $prefix, callable $callback): void
    {
        $previous = self::$prefix;
        self::$prefix .= $prefix;

        $callback();

        self::$prefix = $previous;
    }

    /**
     * Register routes that share common middleware.
     *
     * @param array    $middleware
     * @param callable $callback
     */
    public static function middleware(array $middleware, callable $callback): void
    {
        $previous = self::$groupMiddleware;

        self::$groupMiddleware = array_merge(self::$groupMiddleware, $middleware);

        $callback();

        self::$groupMiddleware = $previous;
    }

    /**
     * Group routes with a shared prefix and/or middleware.
     *
     * Combines prefix() and middleware() for convenience:
     *
     *     Router::group(['prefix' => '/admin', 'middleware' => ['auth']], function () {
     *         Router::get('/dashboard', [DashboardController::class, 'index']);
     *     });
     *
     * @param array{prefix?: string, middleware?: array<int, string>} $attributes
     * @param callable $callback
     */
    public static function group(array $attributes, callable $callback): void
    {
        $previousPrefix     = self::$prefix;
        $previousMiddleware = self::$groupMiddleware;

        if (isset($attributes['prefix'])) {
            self::$prefix .= $attributes['prefix'];
        }

        if (isset($attributes['middleware'])) {
            self::$groupMiddleware = array_merge(
                self::$groupMiddleware,
                $attributes['middleware']
            );
        }

        $callback();

        self::$prefix          = $previousPrefix;
        self::$groupMiddleware = $previousMiddleware;
    }

    // ──────────────────────────────────────────────────────────────────
    // Query
    // ──────────────────────────────────────────────────────────────────

    /**
     * Return all registered routes.
     *
     * @return array<string, array<string, Route>>
     */
    public static function routes(): array
    {
        return self::$routes;
    }

    /**
     * Get the currently matched route.
     *
     * @return Route|null
     */
    public static function current(): ?Route
    {
        return self::$current;
    }

    /**
     * Generate a URL for a named route.
     *
     * @param string               $name
     * @param array<string, mixed> $parameters
     * @return string
     */
    public static function route(string $name, array $parameters = []): string
    {
        if (self::$nameMap === null) {
            self::$nameMap = [];

            foreach (self::$routes as $method => $routes) {
                foreach ($routes as $uri => $route) {
                    if ($route->name !== null) {
                        self::$nameMap[$route->name] = $uri;
                    }
                }
            }
        }

        if (!isset(self::$nameMap[$name])) {
            return '';
        }

        $url = self::$nameMap[$name];

        foreach ($parameters as $key => $value) {
            $url = str_replace("{{$key}}", rawurlencode((string) $value), $url);
        }

        return $url;
    }

    // ──────────────────────────────────────────────────────────────────
    // Middleware Groups
    // ──────────────────────────────────────────────────────────────────

    /**
     * Register a named middleware group (e.g. "web", "api").
     *
     * @param string                          $name
     * @param array<int, class-string|string> $middleware
     */
    public static function middlewareGroup(string $name, array $middleware): void
    {
        self::$middlewareGroups[$name] = $middleware;
        // Invalidate expanded cache when groups change.
        self::$expandedMiddlewareCache = [];
    }

    /**
     * Expand group names in a middleware list into their members.
     *
     * Detects circular references to prevent infinite recursion.
     *
     * @param array<int, class-string|string> $list
     * @param array<string, bool>             $resolving  Groups currently being resolved (internal).
     * @return array<int, class-string|string>
     *
     * @throws \RuntimeException When a circular middleware group reference is detected.
     */
    public static function expandMiddleware(array $list, array $resolving = []): array
    {
        // Use cache for top-level calls (no resolving context = not recursive).
        if ($resolving === []) {
            $cacheKey = implode('|', $list);
            if (isset(self::$expandedMiddlewareCache[$cacheKey])) {
                return self::$expandedMiddlewareCache[$cacheKey];
            }
        }

        $expanded = [];

        foreach ($list as $item) {
            if (isset(self::$middlewareGroups[$item])) {
                if (isset($resolving[$item])) {
                    throw new \RuntimeException(
                        "Circular middleware group reference detected: '{$item}' "
                        . 'is referenced recursively. Check your middlewareGroups configuration.'
                    );
                }

                $resolving[$item] = true;

                $groupExpanded = self::expandMiddleware(self::$middlewareGroups[$item], $resolving);
                foreach ($groupExpanded as $entry) {
                    $expanded[] = $entry;
                }

                unset($resolving[$item]);
                continue;
            }

            $expanded[] = $item;
        }

        // Cache at top-level only.
        if (isset($cacheKey)) {
            self::$expandedMiddlewareCache[$cacheKey] = $expanded;
        }

        return $expanded;
    }

    // ──────────────────────────────────────────────────────────────────
    // Route Cache (Production)
    // ──────────────────────────────────────────────────────────────────

    /**
     * Load routes from a compiled cache file.
     *
     * The cache file should return a serialized route table array.
     * When loaded successfully, routes registration from config/routes.php is skipped.
     *
     * @param string $cacheFile Absolute path to the route cache file.
     * @return bool True if the cache was loaded.
     */
    public static function loadCache(string $cacheFile): bool
    {
        if (!is_file($cacheFile)) {
            return false;
        }

        $data = require $cacheFile;
        if (!is_array($data) || !isset($data['routes'])) {
            return false;
        }

        self::$routes = $data['routes'];
        self::$staticRoutes = $data['static'] ?? [];
        self::$dynamicRoutes = $data['dynamic'] ?? [];
        self::$compiledPatterns = $data['patterns'] ?? [];

        return true;
    }

    /**
     * Compile and save the current route table to a cache file.
     *
     * @param string $cacheFile Absolute path to write the cache.
     */
    public static function saveCache(string $cacheFile): void
    {
        // Ensure static/dynamic indexes are built.
        self::rebuildRouteIndex();

        $dir = dirname($cacheFile);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $data = [
            'routes'   => self::$routes,
            'static'   => self::$staticRoutes,
            'dynamic'  => self::$dynamicRoutes,
            'patterns' => self::$compiledPatterns,
        ];

        file_put_contents(
            $cacheFile,
            '<?php return ' . var_export($data, true) . ';',
            LOCK_EX
        );
    }

    // ──────────────────────────────────────────────────────────────────
    // Dispatch
    // ──────────────────────────────────────────────────────────────────

    /**
     * Dispatch the current request to the matching route.
     *
     * @param string|null $basePath  Project root (defaults to getcwd()).
     */
    public static function dispatch(?string $basePath = null): void
    {
        try {
            $method = $_SERVER['REQUEST_METHOD'];
            $uri    = self::resolveRequestUri();

            $projectPath = $basePath ?? getcwd();
            if ($projectPath === false) {
                return;
            }

            $frameworkPath = dirname(__DIR__, 3);

            self::loadRoutes($projectPath);

            [$route, $params] = self::matchRoute($method, $uri);

            self::$current = $route;

            if (!$route) {
                ErrorRenderer::render($frameworkPath, 404, null);
                return;
            }

            self::executeMiddleware($route);

            $result = self::callAction($route, $params);

            self::sendResponse($result);
        } catch (\Throwable $e) {
            $frameworkPath = dirname(__DIR__, 3);
            $code = ErrorRenderer::resolveHttpCode($e);

            ErrorRenderer::render($frameworkPath, $code, $e);
        }
    }

    /**
     * Render an HTTP error page.
     *
     * @deprecated Use ErrorRenderer::render() directly. Kept for backward compatibility.
     *
     * @param string         $frameworkPath
     * @param int            $code
     * @param \Throwable|null $e
     */
    public static function renderError(string $frameworkPath, int $code, ?\Throwable $e): void
    {
        ErrorRenderer::render($frameworkPath, $code, $e);
    }

    // ──────────────────────────────────────────────────────────────────
    // Internal Helpers
    // ──────────────────────────────────────────────────────────────────

    /**
     * Add a single route to the registry.
     *
     * @param string                                   $method
     * @param string                                   $uri
     * @param array{0: class-string, 1: string}|\Closure $action
     * @param array<int, string>                        $middleware
     * @return Route
     */
    private static function addRoute(string $method, string $uri, array|\Closure $action, array $middleware): Route
    {
        $uri = self::$prefix . $uri;

        self::invalidateNameMap();

        $mergedMiddleware = self::$groupMiddleware !== [] || $middleware !== []
            ? array_merge(self::$groupMiddleware, $middleware)
            : [];

        $route = new Route($method, $uri, $action, $mergedMiddleware);

        self::$routes[$method][$uri] = $route;

        // Classify as static or dynamic for faster dispatch.
        if (!str_contains($uri, '{')) {
            self::$staticRoutes[$method . '|' . $uri] = $route;
        } else {
            self::$dynamicRoutes[$method][$uri] = $route;

            // Pre-compile the pattern immediately to avoid doing it at dispatch time.
            $cacheKey = $method . '|' . $uri;
            if (!isset(self::$compiledPatterns[$cacheKey])) {
                $pattern = preg_replace(
                    '/\{[a-zA-Z_][a-zA-Z0-9_]*\}/',
                    '([^/]+)',
                    $uri
                );
                self::$compiledPatterns[$cacheKey] = '#^' . $pattern . '$#';
            }
        }

        return $route;
    }

    /**
     * Resolve and normalize the request URI.
     *
     * @return string
     */
    private static function resolveRequestUri(): string
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        if (!is_string($uri)) {
            $uri = '';
        }

        $uri = rtrim($uri, '/');

        return $uri === '' ? '/' : $uri;
    }

    /**
     * Load route definitions from the project's config/routes.php.
     *
     * @param string $projectPath
     */
    private static function loadRoutes(string $projectPath): void
    {
        $routesPath = $projectPath . '/config/routes.php';

        if (file_exists($routesPath)) {
            require_once $routesPath;
        }
    }

    /**
     * Match the request URI against registered routes.
     *
     * Tries static routes first (O(1) hash lookup), then falls back to
     * dynamic pattern matching only on routes that contain parameters.
     *
     * @param string $method  HTTP method.
     * @param string $uri     Normalized request URI.
     * @return array{0: Route|null, 1: array<int, string>}
     */
    private static function matchRoute(string $method, string $uri): array
    {
        // 1. Static route — O(1) lookup, fastest path.
        $staticKey = $method . '|' . $uri;
        if (isset(self::$staticRoutes[$staticKey])) {
            return [self::$staticRoutes[$staticKey], []];
        }

        // 2. Dynamic pattern matching — only check routes with parameters.
        $dynamicRoutes = self::$dynamicRoutes[$method] ?? null;
        if ($dynamicRoutes === null) {
            return [null, []];
        }

        foreach ($dynamicRoutes as $routeUri => $route) {
            $cacheKey = $method . '|' . $routeUri;

            // Pattern is always pre-compiled at registration time now,
            // but keep the fallback for routes loaded from cache without patterns.
            if (!isset(self::$compiledPatterns[$cacheKey])) {
                $pattern = preg_replace(
                    '/\{[a-zA-Z_][a-zA-Z0-9_]*\}/',
                    '([^/]+)',
                    $routeUri
                );
                self::$compiledPatterns[$cacheKey] = '#^' . $pattern . '$#';
            }

            if (preg_match(self::$compiledPatterns[$cacheKey], $uri, $matches)) {
                array_shift($matches);
                return [$route, $matches];
            }
        }

        return [null, []];
    }

    /**
     * Execute all middleware assigned to the matched route.
     *
     * Expands middleware groups and resolves short names to FQCNs.
     * Resolved class names are cached for performance.
     *
     * @param Route $route
     *
     * @throws \RuntimeException If a middleware class cannot be resolved.
     */
    private static function executeMiddleware(Route $route): void
    {
        // Early return: most routes have no middleware.
        if ($route->middleware === []) {
            return;
        }

        foreach (self::expandMiddleware($route->middleware) as $middleware) {
            if (!isset(self::$middlewareClasses[$middleware])) {
                // If the middleware name is already a fully-qualified class, use it directly.
                if (class_exists($middleware)) {
                    self::$middlewareClasses[$middleware] = $middleware;
                } else {
                    $class = "App\\Http\\Middleware\\" . ucfirst($middleware) . "Middleware";

                    if (!class_exists($class)) {
                        throw new \RuntimeException(
                            "Middleware '{$middleware}' could not be resolved: class {$class} not found. "
                            . "Check the route's middleware name and that the class exists under App\\Http\\Middleware."
                        );
                    }

                    self::$middlewareClasses[$middleware] = $class;
                }
            }

            $middlewareClass = self::$middlewareClasses[$middleware];
            /** @var \App\Http\Middleware\Middleware $middlewareInstance */
            $middlewareInstance = new $middlewareClass();
            $middlewareInstance->handle();
        }
    }

    /**
     * Invoke the route's action (closure or controller method).
     *
     * @param Route             $route
     * @param array<int, string> $params  Captured URI parameters.
     * @return mixed
     *
     * @throws \RuntimeException If the controller or method does not exist.
     */
    private static function callAction(Route $route, array $params): mixed
    {
        $action = $route->action;

        if ($action instanceof \Closure) {
            return $action(...$params);
        }

        [$controllerName, $methodName] = $action;

        if (!class_exists($controllerName)) {
            throw new \RuntimeException(
                "Controller {$controllerName} not found. Create it "
                . "(e.g. `php zero make:controller " . ltrim($controllerName, '\\') . "`) "
                . "and ensure Composer autoloading is up to date (`composer dump-autoload`)."
            );
        }

        $container  = App::container();
        $controller = $container->make($controllerName);

        if (!method_exists($controller, $methodName)) {
            throw new \RuntimeException(
                "Method {$methodName}() does not exist on {$controllerName}. "
                . "Check the route's controller action points to a real public method."
            );
        }

        return $controller->$methodName(...$params);
    }

    /**
     * Send the result of a route action as an HTTP response.
     *
     * Supports Response objects, objects with toResponse(), and strings.
     *
     * @param mixed $result
     */
    private static function sendResponse(mixed $result): void
    {
        if ($result instanceof \App\Core\Http\Response) {
            $result->send();
            return;
        }

        if (is_string($result)) {
            echo $result;
            return;
        }

        if (is_object($result) && method_exists($result, 'toResponse')) {
            $response = $result->toResponse();

            if ($response instanceof \App\Core\Http\Response) {
                $response->send();
                return;
            }

            echo (string) $response;
        }
    }

    /**
     * Invalidate the lazy name => uri map so it is rebuilt on next access.
     */
    private static function invalidateNameMap(): void
    {
        self::$nameMap = null;
    }

    /**
     * Rebuild the static/dynamic route index from $routes.
     * Used when saving route cache.
     */
    private static function rebuildRouteIndex(): void
    {
        self::$staticRoutes = [];
        self::$dynamicRoutes = [];

        foreach (self::$routes as $method => $routes) {
            foreach ($routes as $uri => $route) {
                if (!str_contains($uri, '{')) {
                    self::$staticRoutes[$method . '|' . $uri] = $route;
                } else {
                    self::$dynamicRoutes[$method][$uri] = $route;
                }
            }
        }
    }
}
