<?php

use App\Core\Routing\Router;

Router::get('/', [\App\Controllers\HomeController::class, 'index']);
Router::get('/health', [\App\Controllers\HomeController::class, 'health']);

Router::post('/api/login', [\App\Controllers\AuthController::class, 'login']);
Router::get('/api/users', [\App\Controllers\UserController::class, 'index']);
Router::get('/api/users/{id}', [\App\Controllers\UserController::class, 'show']);
