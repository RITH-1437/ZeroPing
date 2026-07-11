<?php

namespace App\Core\Routing;

use App\Core\Application\App;

class Router
{
    private static array $routes = [];
    private static ?Route $current = null;
    private static string $prefix = '';
    private static array $groupMiddleware = [];

    public static function get(
        string $uri,
        array|\Closure $action,
        array $middleware = []
    ): Route {

        $uri = self::$prefix . $uri;

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

    public static function current(): ?Route
    {
        return self::$current;
    }

    public static function route(string $name, array $parameters = []): string
    {
        foreach (self::$routes as $method => $routes) {
            foreach ($routes as $uri => $route) {
                if ($route->name !== null && $route->name === $name) {
                    $url = $uri;
                    foreach ($parameters as $key => $value) {
                        $url = str_replace("{{$key}}", $value, $url);
                    }
                    return $url;
                }
            }
        }

        return '';
    }

    public static function dispatch(?string $basePath = null): void
    {
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

            // Dynamic route matching
            foreach (self::$routes[$method] ?? [] as $routeUri => $routeData) {

                $pattern = preg_replace(
                    '/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/',
                    '([^/]+)',
                    $routeUri
                );

                $pattern = "#^{$pattern}$#";

                if (preg_match($pattern, $uri, $matches)) {

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
            $action(...$params);
            return;
        }

        [$controllerName, $methodName] = $action;

        // Execute middleware
        foreach ($route->middleware as $middleware) {

            $class = "App\\Http\\Middleware\\" . ucfirst($middleware) . "Middleware";

            if (!class_exists($class)) {
                die("Middleware {$class} not found.");
            }

            (new $class())->handle();
        }

        // Controller exists?
        if (!class_exists($controllerName)) {

            http_response_code(500);

            die("Controller {$controllerName} not found.");
        }

        $container = App::container();

        $controller = $container->make($controllerName);

        // Method exists?
        if (!method_exists($controller, $methodName)) {

            http_response_code(500);

            die("Method {$methodName} not found.");
        }

        // Execute controller with route parameters
        $controller->$methodName(...$params);
    }
}
