<?php

declare(strict_types=1);

namespace App\Core\Console\Generators;

class MakeAuthCommand extends Generator
{
    protected string $description = 'Scaffold authentication (controller, views, routes)';

    public function handle(string $name = ''): void
    {
        $controllerName = $this->option('name') ?: ($name ?: 'AuthController');

        $this->generate(
            'auth-controller.stub',
            ['class' => $controllerName],
            BASE_PATH . "/app/Controllers/{$controllerName}.php",
            'Controller'
        );

        $this->generate(
            'auth-login.stub',
            ['app_name' => 'ZeroPing'],
            BASE_PATH . '/views/auth/login.php',
            'View'
        );

        $this->generate(
            'auth-register.stub',
            ['app_name' => 'ZeroPing'],
            BASE_PATH . '/views/auth/register.php',
            'View'
        );

        $routes = <<<PHP

// Auth routes
Router::get('/login', [\App\Controllers\\{$controllerName}::class, 'showLoginForm']);
Router::get('/register', [\App\Controllers\\{$controllerName}::class, 'showRegisterForm']);
Router::post('/login', [\App\Controllers\\{$controllerName}::class, 'login']);
Router::post('/register', [\App\Controllers\\{$controllerName}::class, 'register']);
Router::post('/logout', [\App\Controllers\\{$controllerName}::class, 'logout']);
PHP;

        file_put_contents(BASE_PATH . '/config/routes.php', $routes, FILE_APPEND);

        $this->success('Routes appended: config/routes.php');
    }
}
