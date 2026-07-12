<?php

return [

    'default' => 'mysql',

    'host' => $_ENV['DB_HOST'] ?? '127.0.0.1',

    'port' => $_ENV['DB_PORT'] ?? 3306,

    'database' => $_ENV['DB_NAME'] ?? '',

    'username' => $_ENV['DB_USER'] ?? '',

    'password' => $_ENV['DB_PASS'] ?? '',

    'charset' => $_ENV['DB_CHARSET'] ?? 'utf8mb4',

];
