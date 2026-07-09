<?php

define('ZERO_PING_START', microtime(true));

require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

$app->handle(
    $request = \App\Http\Request::capture()
);
