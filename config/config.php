<?php

use App\Core\Env;

Env::load(__DIR__ . '/../.env');

require_once __DIR__ . '/database.php';
require_once __DIR__ . '/app.php';
require_once __DIR__ . '/constants.php';