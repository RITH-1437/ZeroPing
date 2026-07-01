<?php

namespace App\Core;
class Router
{
    private static array $routes = [];

    public static function get(
        string $uri,
        array $action,
        array $middleware = []
    ): void {
        self::$routes['GET'][$uri] = [
            'action' => $action,
            'middleware' => $middleware
        ];
    }

    public static function post(
        string $uri,
        array $action,
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

        $uri = rtrim($uri, '/');

        if ($uri === '') {
            $uri = '/';
        }

        require_once __DIR__ . '/../../config/routes.php';

        if (!isset(self::$routes[$method][$uri])) {

            http_response_code(404);

            require_once __DIR__ . '/../../views/errors/404.php';

            return;
        }

        $route = self::$routes[$method][$uri];

        [$controllerName, $methodName] = $route['action'];

        $middlewares = $route['middleware'];

        foreach ($middlewares as $middleware) {

            $class = "App\\Middleware\\" . ucfirst($middleware) . "Middleware";

            if (!class_exists($class)) {

                die("Middleware {$class} not found.");
            }

            (new $class())->handle();
        }

        if (!class_exists($controllerName)) {

            http_response_code(500);

            die("Controller {$controllerName} not found.");
        }

        $controller = new $controllerName();

        if (!method_exists($controller, $methodName)) {

            http_response_code(500);

            die("Method {$methodName} not found.");
        }

//
        call_user_func([$controller, $methodName]);
    }
}