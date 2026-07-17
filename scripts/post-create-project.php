<?php

/**
 * ZeroPing post-create-project installer.
 *
 * Runs automatically after `composer create-project` to prepare a fresh
 * installation. It verifies the environment, creates the required runtime
 * directories, copies the environment template, generates the application
 * key, and prints a friendly welcome message.
 *
 * This script never throws stack traces: every failure is reported as a
 * clean, actionable message so the first-run experience stays friendly.
 */

declare(strict_types=1);

$root = dirname(__DIR__);

// Run from the project root so relative paths resolve correctly.
chdir($root);

/**
 * Minimal ANSI colour helpers (auto-disabled on non-TTY / NO_COLOR).
 */
$supportsColor = (function (): bool {
    if (getenv('NO_COLOR') !== false) {
        return false;
    }
    if (DIRECTORY_SEPARATOR === '\\') {
        return getenv('ANSICON') !== false
            || getenv('WT_SESSION') !== false
            || getenv('TERM_PROGRAM') === 'vscode'
            || (function_exists('sapi_windows_vt100_support') && @sapi_windows_vt100_support(STDOUT));
    }
    return true;
})();

$c = function (string $text, string $code) use ($supportsColor): string {
    return $supportsColor ? "\033[{$code}m{$text}\033[0m" : $text;
};

$line   = static fn (string $m = '') => print($m . PHP_EOL);
$ok     = static fn (string $m) => print("  " . $GLOBALS['c']('âœ”', '32') . " {$m}" . PHP_EOL);
$warnFn = static fn (string $m) => print("  " . $GLOBALS['c']('!', '33') . " {$m}" . PHP_EOL);
$errFn  = static fn (string $m) => print("  " . $GLOBALS['c']('âœ—', '31') . " {$m}" . PHP_EOL);

$warnings = [];
$errors   = [];

/**
 * Header banner.
 */
$line('');
$line($c('  ZeroPing installer', '1;36'));
$line($c('  â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€', '90'));
$line('');

/* â”€â”€ 1. Verify PHP version â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
if (version_compare(PHP_VERSION, '8.1.0', '>=')) {
    $ok('PHP ' . PHP_VERSION);
} else {
    $errFn('PHP ' . PHP_VERSION . ' detected â€” ZeroPing requires PHP 8.1 or higher.');
    $errors[] = 'Upgrade PHP to 8.1+ and re-run: composer create-project rith-1437/zeroping';
}

/* â”€â”€ 2. Verify required extensions â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
$required = ['pdo', 'mbstring', 'json', 'ctype', 'tokenizer', 'fileinfo', 'openssl', 'hash'];
$missing  = array_values(array_filter($required, static fn ($ext) => !extension_loaded($ext)));

if ($missing === []) {
    $ok('Required PHP extensions loaded');
} else {
    $errFn('Missing PHP extensions: ' . implode(', ', $missing));
    $errors[] = 'Enable the missing extensions in your php.ini, then run: php zero doctor';
}

if (!extension_loaded('pdo_mysql') && !extension_loaded('pdo_sqlite')) {
    $warnFn('No PDO database driver detected (pdo_mysql / pdo_sqlite).');
    $warnings[] = 'Install a PDO driver before connecting to a database.';
}

/* â”€â”€ 3. Verify Composer version â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
$composerVer = defined('Composer\\Composer::VERSION') ? \Composer\Composer::VERSION : null;
if ($composerVer !== null && $composerVer !== '@package_version@') {
    if (version_compare($composerVer, '2.0.0', '>=')) {
        $ok('Composer ' . $composerVer);
    } else {
        $warnFn('Composer ' . $composerVer . ' detected â€” Composer 2.x is recommended.');
        $warnings[] = 'Upgrade Composer: composer self-update';
    }
} else {
    $ok('Composer detected');
}

/* â”€â”€ 4. Create writable runtime directories â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
$dirs = [
    'storage/cache',
    'storage/cache/views',
    'storage/logs',
    'storage/framework',
    'bootstrap/cache',
];

$dirOk = true;
foreach ($dirs as $dir) {
    $path = $root . '/' . $dir;
    if (!is_dir($path)) {
        @mkdir($path, 0775, true);
    }
    if (!is_dir($path) || !is_writable($path)) {
        $dirOk = false;
        $errFn("Directory not writable: {$dir}");
        $errors[] = "Grant write permission to: {$dir}";
    }
}
if ($dirOk) {
    $ok('Runtime directories are writable');
}

/* â”€â”€ 5. Create .env from .env.example â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
$envPath     = $root . '/.env';
$examplePath = $root . '/.env.example';

if (!file_exists($envPath) && file_exists($examplePath)) {
    if (@copy($examplePath, $envPath)) {
        $ok('Created .env from .env.example');
    } else {
        $errFn('Could not create .env (permission denied)');
        $errors[] = 'Copy .env.example to .env manually.';
    }
} elseif (file_exists($envPath)) {
    $ok('.env already exists');
} else {
    $warnFn('.env.example is missing; skipping .env creation');
    $warnings[] = 'Create a .env file before running the application.';
}

/* ── 6. Personalize project metadata ────────────────────────────────────────────────────────── */
$autoloadPath = $root . '/vendor/autoload.php';
if (file_exists($autoloadPath)) {
    require_once $autoloadPath;
    if (class_exists(\App\Core\Console\ProjectMetadataGenerator::class, false)) {
        try {
            $projectName = $_ENV['APP_NAME'] ?? getenv('APP_NAME') ?: 'ZeroPing';
            $starterType = $_ENV['STARTER_TYPE'] ?? getenv('STARTER_TYPE') ?: 'mvc';
            $appVersion = defined(\App\Core\Application\App::class . '::VERSION')
                ? \App\Core\Application\App::VERSION
                : '2.0.0';
            $gen = new \App\Core\Console\ProjectMetadataGenerator(
                projectName: $projectName,
                starterType: $starterType,
                frameworkVersion: $appVersion,
            );
            $gen->replaceInDirectory($root);
            $gen->brandEnv($root . '/.env');
            $gen->brandComposerJson($root . '/composer.json');
            $gen->generateReadme($root . '/README.md');
            $gen->generateAssets($root . '/public');
            $ok('Project personalized');
        } catch (\Throwable $e) {
            $warnFn('Could not personalize project metadata');
            $warnings[] = 'Run: php zero project:brand to set your project name';
        }
    }
}
if (file_exists($envPath)) {
    $env = (string) file_get_contents($envPath);

    if (preg_match('/^APP_KEY=(.+)$/m', $env, $m) && trim($m[1]) !== '' && trim($m[1]) !== 'base64:') {
        $ok('Application key already set');
    } else {
        if (!preg_match('/^APP_KEY=/m', $env)) {
            $env = rtrim($env) . "\nAPP_KEY=\n";
        }

        try {
            $key = 'base64:' . base64_encode(random_bytes(32));
            $env = preg_replace('/^APP_KEY=.*$/m', 'APP_KEY=' . $key, $env);
            if (@file_put_contents($envPath, $env) !== false) {
                $ok('Application key generated');
            } else {
                $warnFn('Could not write the application key');
                $warnings[] = 'Run: php zero key:generate';
            }
        } catch (\Throwable $e) {
            $warnFn('Could not generate an application key automatically');
            $warnings[] = 'Run: php zero key:generate';
        }
    }
}

/* 7. Create the SQLite database file (default engine) */
$dbDir  = $root . '/database';
$dbFile = $dbDir . '/database.sqlite';

if (file_exists($dbFile)) {
    $ok('SQLite database already exists');
} elseif (!extension_loaded('pdo_sqlite')) {
    $warnFn('Skipped SQLite database creation (pdo_sqlite not loaded).');
    $warnings[] = 'Enable pdo_sqlite, then run: php zero install';
} else {
    if (!is_dir($dbDir)) {
        @mkdir($dbDir, 0775, true);
    }
    if (@touch($dbFile)) {
        $ok('Created database/database.sqlite');
    } else {
        $warnFn('Could not create database/database.sqlite');
        $warnings[] = 'Create database/database.sqlite (or run: php zero install).';
    }
}

/* 8. Optimise the Composer autoloader */
$composerBin = (PHP_OS_FAMILY === 'Windows') ? 'composer.bat' : 'composer';
$result = @shell_exec(escapeshellcmd($composerBin) . ' dump-autoload --no-interaction 2>&1');

if ($result !== null && (str_contains($result, 'Generated') || str_contains($result, 'autoload'))) {
    $ok('Autoloader optimised');
} else {
    $warnFn('Could not optimise the autoloader automatically.');
    $warnings[] = 'Run: composer dump-autoload';
}

/* â”€â”€ Summary â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
$line('');

if ($errors !== []) {
    $line($c('  Installation completed with errors:', '1;31'));
    foreach (array_unique($errors) as $e) {
        $line('    â€¢ ' . $e);
    }
    $line('');
    $line('  Run ' . $c('php zero doctor', '36') . ' after resolving the issues above.');
    $line('');
    exit(1);
}

if ($warnings !== []) {
    $line($c('  Installation complete â€” with a few notes:', '1;33'));
    foreach (array_unique($warnings) as $w) {
        $line('    â€¢ ' . $w);
    }
    $line('');
}

$rendererLoaded = false;
$autoloadPath = $root . '/vendor/autoload.php';
if (file_exists($autoloadPath)) {
    require_once $autoloadPath;
    if (class_exists(\App\Core\Console\InstallerSuccessRenderer::class, false)) {
        try {
            $appVersion = \App\Core\Application\App::VERSION;
            $envContent = file_exists($root . '/.env') ? file_get_contents($root . '/.env') : '';
            $envVars = [];
            foreach (explode("\n", $envContent ?: '') as $line) {
                $line = trim($line);
                if ($line !== '' && !str_starts_with($line, '#')) {
                    $parts = explode('=', $line, 2);
                    if (count($parts) === 2) {
                        $envVars[$parts[0]] = $parts[1];
                    }
                }
            }
            $projectName = $envVars['APP_NAME'] ?? 'ZeroPing';
            $starterType = $envVars['STARTER_TYPE'] ?? 'empty';
            $r = new \App\Core\Console\InstallerSuccessRenderer(
                projectName: $projectName,
                starterType: $starterType,
                frameworkVersion: $appVersion,
                phpVersion: PHP_VERSION,
                projectPath: $root,
            );
            echo "\n" . $r->render() . "\n";
            $rendererLoaded = true;
        } catch (\Throwable $e) {
            $rendererLoaded = false;
        }
    }
}

if (!$rendererLoaded) {
    $line($c('  âœ” ZeroPing is ready!', '1;32'));
    $line('');
    $line('  Next steps:');
    $line('    ' . $c('php zero serve', '36') . '     Start the development server');
    $line('    ' . $c('php zero install', '36') . '   Run the interactive setup wizard (optional)');
    $line('    ' . $c('php zero doctor', '36') . '    Verify your environment any time');
    $line('');
    $line('  Then open ' . $c('http://localhost:1437', '36') . ' in your browser.');
    $line('');
    $line('  Documentation: ' . $c('https://github.com/RITH-1437/ZeroPing/tree/main/docs', '90'));
    $line('');
}
