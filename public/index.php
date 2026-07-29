<?php

declare(strict_types=1);

error_reporting(E_ALL);

define('ZERO_PING_START', microtime(true));
define('BASE_PATH', realpath(__DIR__ . '/..'));

require_once BASE_PATH . '/vendor/autoload.php';
require_once BASE_PATH . '/app/Helpers/helpers.php';

$app = require_once BASE_PATH . '/bootstrap/app.php';
ini_set('display_errors', config('app.debug', false) ? '1' : '0');

$app->handle(\App\Http\Request::capture());
