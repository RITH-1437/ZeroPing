<?php

define('ZERO_PING_START', microtime(true));

define('BASE_PATH', realpath(__DIR__ . '/..'));

require_once BASE_PATH . '/vendor/autoload.php';
require_once BASE_PATH . '/app/Helpers/helpers.php';

$app = require_once BASE_PATH . '/bootstrap/app.php';

// In the framework repo, serve the official website from framework-site/ so
// its views, components and layouts are loaded from there. Generated projects
// do not ship framework-site/, so they inherit the default base-path (project
// root) and only ever see their own application views/.
if (is_dir(BASE_PATH . '/framework-site')) {
    \App\Core\View\View::setBasePath(BASE_PATH . '/framework-site');
}

$app->handle(
    $request = \App\Http\Request::capture()
);
