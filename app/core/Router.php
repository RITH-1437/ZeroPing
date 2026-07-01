<?php

namespace App\Core;

class Router
{
    private static array $routes = [];

    private static string $prefix = '';

    private static array $groupMiddleware = [];

    public static function get(
        string $uri,
        array $action,
        array $middleware = []
    ): void {

        $uri = self::$prefix . $uri;

        self::$routes['GET'][$uri] = [
            'action' => $action,
            'middleware' => array_merge(
                self::$groupMiddleware,
                $middleware
            ),
        ];
    }

    public static function post(
        string $uri,
        array $action,
        array $middleware = []
    ): void {

        $uri = self::$prefix . $uri;

        self::$routes['POST'][$uri] = [
            'action' => $action,
            'middleware' => array_merge(
                self::$groupMiddleware,
                $middleware
            ),
        ];
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

    public static function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];

        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        $uri = rtrim($uri, '/');

        if ($uri === '') {
            $uri = '/';
        }

        require_once __DIR__ . '/../../config/routes.php';

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

        // Route not found
        if (!$route) {

            http_response_code(404);

            require_once __DIR__ . '/../../views/errors/404.php';

            return;
        }

        [$controllerName, $methodName] = $route['action'];

        // Execute middleware
        foreach ($route['middleware'] as $middleware) {

            $class = "App\\Middleware\\" . ucfirst($middleware) . "Middleware";

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

        $container = App::Container();

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