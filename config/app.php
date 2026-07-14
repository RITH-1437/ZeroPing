<?php

return [

    'name' => $_ENV['APP_NAME'] ?? 'ZeroPing',

    'env' => $_ENV['APP_ENV'] ?? 'local',

    'debug' => ($_ENV['APP_DEBUG'] ?? 'false') === 'true',

    'locale' => $_ENV['APP_LOCALE'] ?? 'en',

    'fallback_locale' => 'en',

    'providers' => [
        \App\Providers\AppServiceProvider::class,
        \App\Providers\ConfigServiceProvider::class,
        \App\Providers\DatabaseServiceProvider::class,
        \App\Providers\EventServiceProvider::class,
        \App\Providers\CacheServiceProvider::class,
        \App\Providers\FilesystemServiceProvider::class,
        \App\Providers\LoggingServiceProvider::class,
        \App\Providers\MailServiceProvider::class,
        \App\Providers\QueueServiceProvider::class,
        \App\Providers\ScheduleServiceProvider::class,
        \App\Providers\NotificationServiceProvider::class,
        \App\Providers\ValidationServiceProvider::class,
        \App\Providers\LocalizationServiceProvider::class,
        \App\Providers\DebugBarServiceProvider::class,
    ],

];
