<?php

return [

    'key' => $_ENV['APP_KEY'] ?? '',

    'hash_driver' => 'bcrypt',

    'rate_limits' => [
        'api' => '60,1',
        'web' => '60,1',
    ],

    'csrf_lifetime' => 120,

];
