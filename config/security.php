<?php

declare(strict_types=1);

$appKey = getenv('APP_KEY') ?: '';

if ($appKey === '' || $appKey === 'base64:') {
    $isCi = getenv('CI') !== false || getenv('GITHUB_ACTIONS') !== false;
    if (!$isCi) {
        trigger_error('APP_KEY is not set. Run "php zero key:generate" to generate a secure key.', E_USER_WARNING);
    }
}

return [

    'key' => $appKey,

    'hash_driver' => 'bcrypt',

    'rate_limits' => [
        'api' => '60,1',
        'web' => '60,1',
    ],

    'csrf_lifetime' => 120,

];
