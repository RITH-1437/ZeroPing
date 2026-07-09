<?php

use App\Core\Routing\Router;

Router::post('/api/login', [\App\Controllers\AuthController::class, 'login']);
Router::get('/api/users', [\App\Controllers\UserController::class, 'index']);
Router::get('/api/users/{id}', [\App\Controllers\UserController::class, 'show']);
