<?php

use App\Controllers\HomeController;
use App\Core\Application\App;
use App\Core\Console\WelcomeController;
use App\Core\Logging\Logger;
use App\Core\Routing\Router;

/*
|--------------------------------------------------------------------------
| Default homepage
|--------------------------------------------------------------------------
|
| In a fresh installation the marketing site is not shipped, so the root
| URL falls back to a clean, self-contained welcome page. The full
| ZeroPing marketing site (WebsiteController + views/site) is only
| available in the repository itself.
|
*/

if (class_exists(\App\Controllers\WebsiteController::class)) {
    Router::get('/', [\App\Controllers\WebsiteController::class, 'home']);
    Router::get('/features', [\App\Controllers\WebsiteController::class, 'features']);
    Router::get('/documentation', [\App\Controllers\WebsiteController::class, 'documentation']);
    Router::get('/installation', [\App\Controllers\WebsiteController::class, 'installation']);
    Router::get('/getting-started', [\App\Controllers\WebsiteController::class, 'gettingStarted']);
    Router::get('/api', [\App\Controllers\WebsiteController::class, 'api']);
    Router::get('/roadmap', [\App\Controllers\WebsiteController::class, 'roadmap']);
    Router::get('/github', [\App\Controllers\WebsiteController::class, 'github']);
    Router::get('/docs/{slug}', [\App\Controllers\WebsiteController::class, 'docs']);
    Router::get('/docs', [\App\Controllers\WebsiteController::class, 'documentation']);
} else {
    Router::get('/', [WelcomeController::class, 'index']);
}

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

if (class_exists(\App\Controllers\SearchController::class)) {
    Router::get('/search', [\App\Controllers\SearchController::class, 'search']);
    Router::get('/search/build', [\App\Controllers\SearchController::class, 'build']);
}

if (class_exists(\App\Controllers\CoffeeController::class)) {
    Router::get('/coffee', [\App\Controllers\CoffeeController::class, 'index']);
}

Router::get('/log-test', function () {
    $logger = App::container()->make(Logger::class);

    $logger->info('Logger is working.');
    $logger->warning('Testing warning.');
    $logger->error('Testing error.');

    echo "Log created successfully.";
});
