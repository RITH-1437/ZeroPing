<?php

use App\Core\Routing\Router;

Router::get('/', [\App\Core\Console\WelcomeController::class, 'index']);
Router::get('/users', [\App\Controllers\UserController::class, 'index']);
Router::get('/users/create', [\App\Controllers\UserController::class, 'create']);
Router::post('/users', [\App\Controllers\UserController::class, 'store']);
