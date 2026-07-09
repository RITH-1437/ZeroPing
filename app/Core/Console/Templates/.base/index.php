<?php

define('ZERO_PING_START', microtime(true));

require_once __DIR__ . '/vendor/autoload.php';

$app = new App\Core\Application\App(__DIR__);

$app->handle(\App\Http\Request::capture());
