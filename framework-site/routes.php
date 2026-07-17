<?php

use App\Core\Routing\Router;
use FrameworkSite\SearchController;
use FrameworkSite\WebsiteController;

/*
 * Framework website routes.
 *
 * This file is loaded ONLY when running the ZeroPing repository itself
 * (i.e. when framework-site/ is present). Generated applications do not ship
 * framework-site/, so these routes are never registered for them.
 */

Router::get('/', [WebsiteController::class, 'home']);
Router::get('/features', [WebsiteController::class, 'features']);
Router::get('/installation', [WebsiteController::class, 'installation']);
Router::get('/getting-started', [WebsiteController::class, 'gettingStarted']);
Router::get('/docs', [WebsiteController::class, 'documentation']);
Router::get('/docs/{page}', fn($page) => (new WebsiteController())->docs($page));
Router::get('/api', [WebsiteController::class, 'api']);
Router::get('/roadmap', [WebsiteController::class, 'roadmap']);
Router::get('/github', [WebsiteController::class, 'github']);
Router::get('/community', [WebsiteController::class, 'community']);
Router::get('/search', [SearchController::class, 'search']);
