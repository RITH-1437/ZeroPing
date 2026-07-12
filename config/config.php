<?php

if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}

require_once __DIR__ . '/../app/Core/Config/Env.php';
require_once BASE_PATH . '/app/Helpers/helpers.php';
use App\Core\Config\Env;

Env::load(BASE_PATH . '/.env');

require_once __DIR__ . '/constants.php';
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/app.php';
