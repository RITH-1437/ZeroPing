<?php

use App\Controllers\HomeController;
use App\Controllers\WebsiteController;
use App\Core\Application\App;
use App\Core\Logging\Logger;
use App\Core\Routing\Router;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Router::get('/', [WebsiteController::class, 'home']);
Router::get('/features', [WebsiteController::class, 'features']);
Router::get('/documentation', [WebsiteController::class, 'documentation']);
Router::get('/installation', [WebsiteController::class, 'installation']);
Router::get('/getting-started', [WebsiteController::class, 'gettingStarted']);
Router::get('/api', [WebsiteController::class, 'api']);
Router::get('/roadmap', [WebsiteController::class, 'roadmap']);
Router::get('/github', [WebsiteController::class, 'github']);
Router::get('/docs/{slug}', [WebsiteController::class, 'docs']);
Router::get('/docs', [WebsiteController::class, 'documentation']);

Router::get('/about', [HomeController::class, 'about']);
Router::get('/session', [HomeController::class, 'session']);
Router::get('/search', [\App\Controllers\SearchController::class, 'search']);
Router::get('/search/build', [\App\Controllers\SearchController::class, 'build']);

Router::get('/log-test', function () {
    $logger = App::container()->make(Logger::class);

    $logger->info('Logger is working.');
    $logger->warning('Testing warning.');
    $logger->error('Testing error.');

    echo "Log created successfully.";
});
