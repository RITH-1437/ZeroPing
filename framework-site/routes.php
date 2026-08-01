<?php

use App\Core\Routing\Router;
use FrameworkSite\SearchController;
use FrameworkSite\WebsiteController;

/* Official ZeroPing framework-site routes. These are registered only from the repository. */
Router::get('/', [WebsiteController::class, 'home']);
Router::get('/arena', [WebsiteController::class, 'arena']);
Router::get('/features', [WebsiteController::class, 'features']);
Router::get('/installation', [WebsiteController::class, 'installation']);
Router::get('/getting-started', [WebsiteController::class, 'gettingStarted']);
Router::get('/docs', [WebsiteController::class, 'documentation']);
Router::get('/docs/{page}', fn ($page) => (new WebsiteController())->docs($page));
Router::get('/api', [WebsiteController::class, 'api']);
Router::get('/packages', [WebsiteController::class, 'packages']);
Router::get('/examples', [WebsiteController::class, 'examples']);
Router::get('/changelog', [WebsiteController::class, 'changelog']);
Router::get('/blog', [WebsiteController::class, 'blog']);
Router::get('/roadmap', [WebsiteController::class, 'roadmap']);
Router::get('/github', [WebsiteController::class, 'github']);
Router::get('/community', [WebsiteController::class, 'community']);
Router::get('/sponsors', [WebsiteController::class, 'sponsors']);
Router::get('/search', [SearchController::class, 'search']);

Router::get('/showcase', [WebsiteController::class, 'showcase']);
Router::get('/deploy', [WebsiteController::class, 'deploy']);
Router::get('/studio', [WebsiteController::class, 'studio']);
Router::get('/cloud', [WebsiteController::class, 'cloud']);
Router::get('/forge', [WebsiteController::class, 'forge']);
Router::get('/up', static function () {
    return response()->json(['status' => 'ok', 'timestamp' => time()])->send();
});
