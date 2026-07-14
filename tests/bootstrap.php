<?php

/**
 * PHPUnit bootstrap for the ZeroPing framework test-suite.
 *
 * Loads the framework (helpers, env, config, container) when present so
 * database / ORM tests can resolve configuration through the same code path
 * as a running application.
 */

require_once __DIR__ . '/../vendor/autoload.php';

if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}

$frameworkBootstrap = __DIR__ . '/../bootstrap/app.php';

if (file_exists($frameworkBootstrap)) {
    // The framework bootstrap constructs App, whose constructor calls
    // bootstrap() — this defines helpers (incl. config()), loads .env and
    // registers the config repository. Models / Schema rely on these.
    require_once $frameworkBootstrap;
}
