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

    public static function prefix(
        string $prefix,
        callable $callback
    ): void {

        $previous = self::$prefix;

        self::$prefix .= $prefix;

        $callback();

        self::$prefix = $previous;
    }

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

    public static function routes(): array
    {
        return self::$routes;
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

    public static function current(): ?Route
    {
        return self::$current;
    }

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
                http_response_code(404);
                $title = '404 - Page Not Found';
                $active = '';
                ob_start();
                require $frameworkPath . '/views/errors/404.php';
                $content = ob_get_clean();
                require $frameworkPath . '/views/layouts/site.php';
                return;
            }

            $action = $route->action;

            if ($action instanceof \Closure) {
                $result = $action(...$params);
                if (is_string($result)) {
                    echo $result;
                }
                return;
            }

            [$controllerName, $methodName] = $action;

            // Execute middleware (resolved class name cached per short name)
            foreach ($route->middleware as $middleware) {
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
            if (is_string($result)) {
                echo $result;
            }
        } catch (\Exception $e) {
            http_response_code(500);
            $title = '500 - Server Error';
            $message = $e->getMessage();
            $trace = $e->getTrace();
            $debug = function_exists('config') ? (bool) config('app.debug', false) : false;

            ob_start();
            require dirname(__DIR__, 3) . '/views/errors/500.php';
            $content = ob_get_clean();
            require dirname(__DIR__, 3) . '/views/layouts/site.php';
        }
    }
}
