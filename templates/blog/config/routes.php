<?php

use App\Core\Routing\Router;

Router::get('/', [\App\Core\Console\WelcomeController::class, 'index']);
Router::get('/blog', [\App\Controllers\PostController::class, 'index']);
Router::get('/blog/{slug}', [\App\Controllers\PostController::class, 'show']);
