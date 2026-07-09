<?php

define('ZERO_PING_START', microtime(true));

define('BASE_PATH', realpath(__DIR__ . '/..'));

require_once BASE_PATH . '/vendor/autoload.php';

$app = require_once BASE_PATH . '/bootstrap/app.php';

$app->handle(
    $request = \App\Http\Request::capture()
);
