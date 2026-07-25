<?php

namespace App\Core\Routing;

use App\Core\Application\App;

class Router
{
    private static array $routes = [];
    private static ?Route $current = null;
    private static string $prefix = '';
    private static array $groupMiddleware = [];

    /**
     * Named middleware groups (e.g. "web", "api"), referenced by
     * name from a route's middleware list.
     *
     * @var array<string, array<int, class-string|string>>
     */
    private static array $middlewareGroups = [];

    /**
     * Compiled regex patterns, cached per "METHOD|uri" so dynamic route
     * matching does not recompile the same pattern on every request.
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
     * Register a GET route.
     *
     * @param string          $uri
     * @param array|\Closure  $action
     * @param array           $middleware
     * @return \App\Core\Routing\Route
     */
    public static function get(
        string $uri,
        array|\Closure $action,
        array $middleware = []
    ): Route {

        $uri = self::$prefix . $uri;

        self::invalidateNameMap();

        return self::$routes['GET'][$uri] = new Route('GET', $uri, $action, array_merge(
            self::$groupMiddleware,
            $middleware
        ));
    }

    /**
     * Register a POST route.
     *
     * @param string          $uri
     * @param array|\Closure  $action
     * @param array           $middleware
     * @return \App\Core\Routing\Route
     */
    public static function post(
        string $uri,
        array|\Closure $action,
        array $middleware = []
    ): Route {

        $uri = self::$prefix . $uri;

        self::invalidateNameMap();

        return self::$routes['POST'][$uri] = new Route('POST', $uri, $action, array_merge(
            self::$groupMiddleware,
            $middleware
        ));
    }

    /**
     * Register routes under a URI prefix.
     *
     * @param string   $prefix
     * @param callable $callback
     */
    public static function prefix(
        string $prefix,
        callable $callback
    ): void {

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
    public static function middleware(
        array $middleware,
        callable $callback
    ): void {

        $previous = self::$groupMiddleware;

        self::$groupMiddleware = array_merge(
            self::$groupMiddleware,
            $middleware
        );

        $callback();

        self::$groupMiddleware = $previous;
    }

    /**
     * Return all registered routes.
     *
     * @return array
     */
    public static function routes(): array
    {
        return self::$routes;
    }

    /**
     * Register a named middleware group (e.g. "web", "api").
     *
     * @param string $name
     * @param array  $middleware
     */
    public static function middlewareGroup(string $name, array $middleware): void
    {
        self::$middlewareGroups[$name] = $middleware;
    }

    /**
     * Expand group names in a middleware list into their members.
     *
     * @param array<int, class-string|string> $list
     * @return array<int, class-string|string>
     */
    public static function expandMiddleware(array $list): array
    {
        $expanded = [];

        foreach ($list as $item) {
            if (isset(self::$middlewareGroups[$item])) {
                $expanded = array_merge(
                    $expanded,
                    self::expandMiddleware(self::$middlewareGroups[$item])
                );

                continue;
            }

            $expanded[] = $item;
        }

        return $expanded;
    }

    /**
     * Invalidate the lazy name => uri map; it is rebuilt on the next route()
     * call. The map is cached for speed, but must be recomputed whenever
     * routes change (e.g. between test cases or late registration).
     */
    private static function invalidateNameMap(): void
    {
        self::$nameMap = null;
    }

    /**
     * Get the currently matched route.
     *
     * @return \App\Core\Routing\Route|null
     */
    public static function current(): ?Route
    {
        return self::$current;
    }

    /**
     * Generate a URL for a named route.
     *
     * @param string $name
     * @param array  $parameters
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
            $url = str_replace("{{$key}}", $value, $url);
        }

        return $url;
    }

    /**
     * Dispatch the current request to the matching route.
     *
     * @param string|null $basePath
     */
    public static function dispatch(?string $basePath = null): void
    {
        try {
            $method = $_SERVER['REQUEST_METHOD'];

            $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

            $uri = rtrim($uri, '/');

            if ($uri === '') {
                $uri = '/';
            }

            $projectPath = $basePath ?? getcwd();
            $frameworkPath = dirname(__DIR__, 3);

            $routesPath = $projectPath . '/config/routes.php';
            if (file_exists($routesPath)) {
                require_once $routesPath;
            }

            $route = null;

            $params = [];

            // Exact route match
            if (isset(self::$routes[$method][$uri])) {
                $route = self::$routes[$method][$uri];
            } else {
                // Dynamic route matching (compiled patterns are cached)
                foreach (self::$routes[$method] ?? [] as $routeUri => $routeData) {
                    $cacheKey = $method . '|' . $routeUri;

                    if (!isset(self::$compiledPatterns[$cacheKey])) {
                        $pattern = preg_replace(
                            '/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/',
                            '([^/]+)',
                            $routeUri
                        );
                        self::$compiledPatterns[$cacheKey] = "#^{$pattern}$#";
                    }

                    if (preg_match(self::$compiledPatterns[$cacheKey], $uri, $matches)) {
                        array_shift($matches);

                        $params = $matches;

                        $route = $routeData;

                        break;
                    }
                }
            }

            self::$current = $route;

            if (!$route) {
                self::renderError($frameworkPath, 404, null);
                return;
            }

            $action = $route->action;

            if ($action instanceof \Closure) {
                $result = $action(...$params);
                self::sendResult($result);
                return;
            }

            [$controllerName, $methodName] = $action;

            // Execute middleware (resolved class name cached per short name)
            foreach (self::expandMiddleware($route->middleware) as $middleware) {
                if (!isset(self::$middlewareClasses[$middleware])) {
                    $class = "App\\Http\\Middleware\\" . ucfirst($middleware) . "Middleware";

                    if (!class_exists($class)) {
                        throw new \RuntimeException(
                            "Middleware '{$middleware}' could not be resolved: class {$class} not found. "
                            . "Check the route's middleware name and that the class exists under App\\Http\\Middleware."
                        );
                    }

                    self::$middlewareClasses[$middleware] = $class;
                }

                (new self::$middlewareClasses[$middleware]())->handle();
            }

            // Controller exists?
            if (!class_exists($controllerName)) {
                throw new \RuntimeException(
                    "Controller {$controllerName} not found. Create it "
                    . "(e.g. `php zero make:controller " . ltrim($controllerName, '\\') . "`) "
                    . "and ensure Composer autoloading is up to date (`composer dump-autoload`)."
                );
            }

            $container = App::container();

            $controller = $container->make($controllerName);

            // Method exists?
            if (!method_exists($controller, $methodName)) {
                throw new \RuntimeException(
                    "Method {$methodName}() does not exist on {$controllerName}. "
                    . "Check the route's controller action points to a real public method."
                );
            }

            // Execute controller with route parameters
            $result = $controller->$methodName(...$params);
            self::sendResult($result);
        } catch (\Throwable $e) {
            $code = in_array($e->getCode(), [401, 403, 404, 419, 429, 500, 503], true)
                ? $e->getCode()
                : 500;

            self::renderError($frameworkPath, $code, $e);
        }
    }

    /**
     * Render a framework error page wrapped in the site layout.
     *
     * Passes a rich debug context so the development view can show the
     * exception, file, line, stack trace, request and environment while
     * the production view stays minimal.
     */
    /**
     * Send a route/controller result as the HTTP response.
     *
     * Supports: App\Core\Http\Response objects, objects exposing toResponse(),
     * and plain strings (echoed as-is).
     */
    protected static function sendResult(mixed $result): void
    {
        if ($result instanceof \App\Core\Http\Response) {
            $result->send();
            return;
        }

        if (is_object($result) && method_exists($result, 'toResponse')) {
            $response = $result->toResponse();

            if ($response instanceof \App\Core\Http\Response) {
                $response->send();
                return;
            }

            echo (string) $response;
            return;
        }

        if (is_string($result)) {
            echo $result;
        }
    }

    /**
     * Render an HTTP error page.
     *
     * @param string           $frameworkPath
     * @param int              $code
     * @param \Throwable|null  $e
     */
    public static function renderError(string $frameworkPath, int $code, ?\Throwable $e): void
    {
        http_response_code($code);

        $message    = $e !== null ? $e->getMessage() : '';
        $exception  = $e !== null ? get_class($e) : '';
        $file       = $e !== null ? $e->getFile() : '';
        $line       = $e !== null ? $e->getLine() : 0;
        $trace      = $e !== null ? $e->getTrace() : [];
        $requestUrl = $_SERVER['REQUEST_URI'] ?? '/';
        $requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $environment = $_ENV['APP_ENV'] ?? 'local';
        $debug      = function_exists('config') ? (bool) config('app.debug', false) : false;
        $active     = '';

        $view = $frameworkPath . '/views/errors/' . $code . '.php';

        if (!file_exists($view)) {
            $view = $frameworkPath . '/views/errors/500.php';
        }

        // The error views under views/errors/ are self-contained documents
        // (they include the shared views/layouts/error.php), so they render
        // standalone without the website "site" layout. This keeps the starter
        // application's error pages dependency-free.
        if (file_exists($view)) {
            require $view;
            return;
        }

        // Last-resort fallback so an error never itself fatals.
        http_response_code($code);
        echo htmlspecialchars((string) $code, ENT_QUOTES) . ' Error';
    }
}
