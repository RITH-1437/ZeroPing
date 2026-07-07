<?php

namespace App\Core\Application;

use App\Core\Routing\Router;

class App
{
    protected string $basePath;

    public function __construct(string $basePath)
    {
        $this->basePath = $basePath;
        $this->bootstrap();
    }

    public function handle($request)
    {
        Router::dispatch();
    }

    protected function bootstrap(): void
    {
        //
    }
}
