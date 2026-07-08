<?php

// Database constants
defined('DB_HOST')    || define('DB_HOST',    $_ENV['DB_HOST']    ?? '127.0.0.1');
defined('DB_NAME')    || define('DB_NAME',    $_ENV['DB_NAME']    ?? '');
defined('DB_USER')    || define('DB_USER',    $_ENV['DB_USER']    ?? '');
defined('DB_PASS')    || define('DB_PASS',    $_ENV['DB_PASS']    ?? '');
defined('DB_CHARSET') || define('DB_CHARSET', $_ENV['DB_CHARSET'] ?? 'utf8mb4');
defined('DB_PORT')    || define('DB_PORT',    $_ENV['DB_PORT']    ?? 3306);

// Application constants
defined('APP_NAME')  || define('APP_NAME',  $_ENV['APP_NAME']  ?? 'ZeroPing');
defined('APP_ENV')   || define('APP_ENV',   $_ENV['APP_ENV']   ?? 'local');
defined('APP_DEBUG') || define('APP_DEBUG', ($_ENV['APP_DEBUG'] ?? 'false') === 'true');
