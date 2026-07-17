<?php

if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}

require_once __DIR__ . '/../app/Core/Config/Env.php';
require_once BASE_PATH . '/app/Helpers/helpers.php';

$envFile = BASE_PATH . '/.env';

if (file_exists($envFile)) {
    \App\Core\Config\Env::load($envFile);
}

require_once __DIR__ . '/constants.php';
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/app.php';