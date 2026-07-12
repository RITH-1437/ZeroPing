<?php

namespace App\Core\Console\Commands;

use Closure;
use App\Core\Console\Command;
use App\Core\Routing\Router;

class RouteListCommand extends Command
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

        $rows = [];

        foreach ($routes as $method => $items) {
            foreach ($items as $uri => $route) {
                $action = $route->action;

                if ($action instanceof Closure) {
                    $actionName = 'Closure';
                } else {
                    [$controller, $methodName] = $action;
                    $controller = class_basename($controller);
                    $actionName = "{$controller}@{$methodName}";
                }

                $middleware = empty($route->middleware)
                    ? '-'
                    : implode(', ', $route->middleware);

                $rows[] = [$method, $uri, $actionName, $middleware];
            }
        }

        $this->title('Registered Routes');

        if ($rows === []) {
            $this->warn('No routes have been registered.');
            return;
        }

        $this->table(
            ['Method', 'URI', 'Action', 'Middleware'],
            $rows
        );

        $this->line('');
        $this->comment('Total: ' . count($rows) . ' route(s).');
    }
}
