<?php

namespace App\Core\Console\Commands;

use App\Core\Routing\Router;

class RouteListCommand
{
    public function handle(): void
    {
        require BASE_PATH . '/config/routes.php';

        $routes = Router::routes();

        echo PHP_EOL;

        printf(
            "%-8s %-30s %-40s %-20s\n",
            "METHOD",
            "URI",
            "ACTION",
            "MIDDLEWARE"
        );

        echo str_repeat('-', 105) . PHP_EOL;

        foreach ($routes as $method => $items) {

            foreach ($items as $uri => $route) {

                [$controller, $action] = $route['action'];

                $controller = class_basename($controller);

                $middleware = implode(
                    ', ',
                    $route['middleware']
                );

                printf(
                    "%-8s %-30s %-40s %-20s\n",
                    $method,
                    $uri,
                    "{$controller}@{$action}",
                    $middleware
                );
            }
        }

        echo PHP_EOL;
    }
}