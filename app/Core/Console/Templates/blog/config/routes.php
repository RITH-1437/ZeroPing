<?php

use App\Core\Routing\Router;

Router::get('/', [\App\Controllers\HomeController::class, 'index']);
Router::get('/blog', [\App\Controllers\PostController::class, 'index']);
Router::get('/blog/{slug}', [\App\Controllers\PostController::class, 'show']);
