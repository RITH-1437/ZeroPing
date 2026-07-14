<?php

use App\Core\Routing\Router;

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you register the routes for your application. The Welcome
| controller powers the default landing page shown on a fresh install.
|
*/

Router::get('/', [\App\Core\Console\WelcomeController::class, 'index']);
Router::get('/cli', [\App\Core\Console\WelcomeController::class, 'cli']);
Router::get('/start', [\App\Core\Console\WelcomeController::class, 'start']);

Router::get('/docs/{page}', fn($page) => (new \App\Core\Docs\Docs())->render($page));

// Auth routes
Router::get('/login', [\App\Controllers\AuthController::class, 'showLoginForm']);
Router::get('/register', [\App\Controllers\AuthController::class, 'showRegisterForm']);
Router::post('/login', [\App\Controllers\AuthController::class, 'login']);
Router::post('/register', [\App\Controllers\AuthController::class, 'register']);
Router::post('/logout', [\App\Controllers\AuthController::class, 'logout']);
