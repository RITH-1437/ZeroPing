<?php

namespace App\Core\Console\Commands;

use Closure;
use App\Core\Routing\Router;

class RouteListCommand
{
    /**
     * The console command description.
     *
     * @var string
     */
    protected string $description = 'List all registered routes';

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

                $action = $route['action'];

                if ($action instanceof Closure) {

                    $actionName = 'Closure';

                } else {

                    [$controller, $methodName] = $action;

                    $controller = class_basename($controller);

                    $actionName = "{$controller}@{$methodName}";
                }

                $middleware = empty($route['middleware'])
                    ? '-'
                    : implode(', ', $route['middleware']);

                printf(
                    "%-8s %-30s %-40s %-20s\n",
                    $method,
                    $uri,
                    $actionName,
                    $middleware
                );
            }
        }

        echo PHP_EOL;
    }
}
