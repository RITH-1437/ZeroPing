<?php

class Router
{
    private static array $routes = [];

    public static function get(
        string $uri,
        string $action,
        array $middleware = []
    ): void {

        self::$routes['GET'][$uri] = [
            'action' => $action,
            'middleware' => $middleware
        ];
    }

    public static function post(
        string $uri,
        string $action,
        array $middleware = []
    ): void {

        self::$routes['POST'][$uri] = [
            'action' => $action,
            'middleware' => $middleware
        ];
    }

    public static function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];

        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // Remove project folder when using PHP built-in server
        $uri = str_replace('/zero-ping/public', '', $uri);

        if ($uri === '') {
            $uri = '/';
        }

        // Load application routes
        require_once __DIR__ . '/../../config/routes.php';

//        echo '<pre>';
//        print_r(self::$routes);
//        die();

        // Route not found
        if (!isset(self::$routes[$method][$uri])) {

            http_response_code(404);

            require_once __DIR__ . '/../../views/errors/404.php';

            return;
        }

        $route = self::$routes[$method][$uri];

        $action = $route['action'];
        $middlewares = $route['middleware'];

        [$controllerName, $methodName] = explode('@', $action);

        foreach ($middlewares as $middleware) {

            $class = ucfirst($middleware) . 'Middleware';

            if (!class_exists($class)) {
                die("Middleware {$class} not found.");
            }

            $instance = new $class();

            $instance->handle();
        }

        // Controller not found
        if (!class_exists($controllerName)) {

            http_response_code(500);

            die("Controller <b>{$controllerName}</b> does not exist.");
        }

        $controller = new $controllerName();

        // Method not found
        if (!method_exists($controller, $methodName)) {

            http_response_code(500);

            die("Method <b>{$methodName}</b> does not exist.");
        }

        call_user_func([$controller, $methodName]);
    }
}