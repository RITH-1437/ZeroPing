<?php

namespace App\Core\Console\Commands;

use Closure;
use App\Core\Console\Command;
use App\Core\Routing\Router;

/**
 * `php zero route:list` — render every registered route as a
 * colour-coded table (Method, URI, Name, Controller, Middleware).
 */
class RouteListCommand extends Command
{
    /**
     * The console command description.
     *
     * @var string
     */
    protected string $description = 'List all registered routes';

    private const METHOD_COLORS = [
        'GET'    => 'green',
        'POST'   => 'blue',
        'PUT'    => 'yellow',
        'PATCH'  => 'yellow',
        'DELETE' => 'red',
    ];

    public function handle(): void
    {
        require BASE_PATH . '/config/routes.php';

        $routes = Router::routes();

        $rows = [];

        foreach ($routes as $method => $items) {
            foreach ($items as $uri => $route) {
                $action = $route->action;

                if ($action instanceof Closure) {
                    $actionName = '<fg=gray>Closure</>';
                } else {
                    [$controller, $methodName] = $action;
                    $controller = class_basename($controller);
                    $actionName = "{$controller}@{$methodName}";
                }

                $name = $route->getName();
                $name = $name === null ? '<fg=gray>-</>' : $name;

                $middleware = empty($route->middleware)
                    ? '<fg=gray>-</>'
                    : implode(', ', $route->middleware);

                $color = self::METHOD_COLORS[$method] ?? 'white';

                $rows[] = [
                    "<fg={$color}>{$method}</>",
                    "<fg=white>{$uri}</>",
                    "<fg=yellow>{$name}</>",
                    "<fg=cyan>{$actionName}</>",
                    "<fg=gray>{$middleware}</>",
                ];
            }
        }

        $this->title('Registered Routes');

        if ($rows === []) {
            $this->warn('No routes have been registered.');
            return;
        }

        $this->table(
            ['Method', 'URI', 'Name', 'Controller', 'Middleware'],
            $rows
        );

        $this->line('');
        $this->comment('Total: ' . count($rows) . ' route(s).');
    }
}
