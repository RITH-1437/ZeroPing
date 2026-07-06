<?php

return [

    'name' => $_ENV['APP_NAME'] ?? 'ZeroPing',

    'env' => $_ENV['APP_ENV'] ?? 'local',

    'debug' => ($_ENV['APP_DEBUG'] ?? 'false') === 'true',

];