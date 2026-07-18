<?php

use App\Core\Routing\Router;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| These are the routes for a generated ZeroPing application. Edit freely —
| add controllers under app/Controllers and register them here.
*/

Router::get('/', [\App\Controllers\HomeController::class, 'index']);
Router::get('/about', [\App\Controllers\HomeController::class, 'about']);
