<?php

/**
 * Composer `post-autoload-dump` hook.
 *
 * Regenerates the ZeroPing package manifest (bootstrap/cache/packages.php)
 * after every `composer dump-autoload` / `composer install` / `composer update`
 * so auto-discovery stays in sync with installed packages.
 */

require_once __DIR__ . '/../vendor/autoload.php';

$base = realpath(__DIR__ . '/../') ?: __DIR__ . '/../';

$repo = new \App\Core\Packages\ProviderRepository(
    $base,
    $base . '/bootstrap/cache/packages.php'
);

$enabled = [];
$configFile = $base . '/config/packages.php';

if (file_exists($configFile)) {
    $enabled = require $configFile;
    $enabled = is_array($enabled) ? $enabled : [];
}

$flag = $_ENV['PACKAGE_AUTO_DISCOVER'] ?? getenv('PACKAGE_AUTO_DISCOVER') ?? 'true';
$autoDiscover = $flag !== 'false' && $flag !== '0';

$manifest = $repo->buildManifest($enabled, $autoDiscover);
$repo->cache($manifest);

echo 'Discovered ' . count($manifest) . " ZeroPing package(s).\n";
