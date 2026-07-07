<?php

namespace App\Core\Debug;

use App\Core\Routing\Router;

class RouteCollector implements Collector
{
    public function getName(): string
    {
        return 'route';
    }

    public function render(): string
    {
        $route = Router::current();
        $name = $route ? $route->getName() : 'N/A';

        return "<span>Route: {$name}</span>";
    }
}
