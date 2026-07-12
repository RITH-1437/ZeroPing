<?php

/**
 * ZeroPing post-create-project installer.
 *
 * Runs automatically after `composer create-project` to prepare a fresh
 * installation: it creates the required runtime directories, copies the
 * environment template, and generates the application key.
 */

declare(strict_types=1);

$root = dirname(__DIR__);

// Run from the project root so relative paths resolve correctly.
chdir($root);

function zpEnsureDir(string $path): void
{
    if (!is_dir($path)) {
        @mkdir($path, 0777, true);
        echo "Created directory: {$path}\n";
    }
}

zpEnsureDir($root . '/storage/cache');
zpEnsureDir($root . '/storage/logs');
zpEnsureDir($root . '/bootstrap/cache');

$envPath = $root . '/.env';
$examplePath = $root . '/.env.example';

if (!file_exists($envPath) && file_exists($examplePath)) {
    copy($examplePath, $envPath);
    echo "Created .env from .env.example\n";
}

if (file_exists($envPath)) {
    $env = (string) file_get_contents($envPath);

    if (preg_match('/^APP_KEY=(.*)$/m', $env, $m) && $m[1] !== '') {
        echo "Application key already set; skipping generation.\n";
    } else {
        if (!preg_match('/^APP_KEY=/m', $env)) {
            $env .= "\nAPP_KEY=\n";
        }

        $key = 'base64:' . base64_encode(random_bytes(32));
        $env = preg_replace('/^APP_KEY=.*$/m', 'APP_KEY=' . $key, $env);

        file_put_contents($envPath, $env);
        echo "Application key generated.\n";
    }
}

echo "ZeroPing installation ready.\n";
echo "Start the development server with: php zero serve\n";
echo "Verify your setup any time with:    php zero doctor\n";
