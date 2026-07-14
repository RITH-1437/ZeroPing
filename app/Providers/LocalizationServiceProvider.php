<?php

declare(strict_types=1);

namespace App\Providers;

use App\Core\Localization\Translator;
use App\Core\Support\Config;

class LocalizationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $basePath = defined('BASE_PATH')
            ? BASE_PATH
            : dirname(__DIR__, 2);

        $locale   = Config::get('app.locale', 'en');
        $fallback = Config::get('app.fallback_locale', 'en');

        $this->container->singleton(
            Translator::class,
            fn () => new Translator($basePath . '/resources/lang', $locale, $fallback)
        );
    }
}
