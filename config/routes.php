<?php

use App\Core\Routing\Router;

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| These are the routes for a GENERATED ZeroPing application. They define the
| project's own behaviour and contain NO framework marketing. The framework
| website (Home / Features / Docs / API / Roadmap / GitHub) lives in
| framework-site/ and is registered separately below — it is never copied
| into generated applications, so its routes never leak into a project.
*/

Router::get('/', [\App\Core\Console\WelcomeController::class, 'index']);

/*
|--------------------------------------------------------------------------
| Framework Website Routes (repository only)
|--------------------------------------------------------------------------
|
| When the ZeroPing repository itself is served, framework-site/ exists and
| its route definitions (the official website) are loaded. Generated
| projects do not ship framework-site/, so this block is skipped entirely.
*/

if (is_dir(BASE_PATH . '/framework-site')) {
    require BASE_PATH . '/framework-site/routes.php';
}
