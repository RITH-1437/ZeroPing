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
