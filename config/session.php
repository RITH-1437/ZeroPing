<?php

declare(strict_types=1);

return [

    'driver' => 'file',

    'lifetime' => 120,

    'cookie' => 'zeroping_session',

    'path' => '/',

    'domain' => null,

    'secure' => ($_ENV['APP_ENV'] ?? 'development') === 'production',

    'httponly' => true,

    'samesite' => 'Lax',

];
