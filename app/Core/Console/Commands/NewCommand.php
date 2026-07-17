<?php

namespace App\Core\Console\Commands;

use App\Core\Console\Banner;
use App\Core\Console\ConsoleStyle;
use App\Core\Console\ProjectMetadataGenerator;
use App\Core\Console\Prompts\Prompt;
use App\Core\Console\Prompts\ChoicePrompt;
use App\Core\Console\Prompts\ConfirmPrompt;
use App\Core\Application\App;

/**
 * `php zero new` — interactive project scaffolder.
 *
 * With no arguments it launches a branded wizard that asks for the
 * project name, type, database driver and a few starter options,
 * shows a live summary, then scaffolds a self-contained ZeroPing
 * application (framework + chosen template) into the target folder.
 *
 * Every prompt is a small reusable class (Prompt / ChoicePrompt /
 * ConfirmPrompt) so adding or re-ordering questions is trivial.
 *
 * It also supports a fully non-interactive mode for scripts/CI:
 *   php zero new "My App" --type=mvc --db=sqlite --auth --tailwind --crud
 */
class NewCommand
{
    private const TEMPLATES = [
        'empty'     => 'Minimal project skeleton with a single welcome page',
        'mvc'       => 'Full MVC CRUD scaffold with user management',
        'blog'      => 'Blog with posts, categories and pagination',
        'api'       => 'RESTful API with authentication boilerplate',
    ];

    private array $templates = [
        'empty'     => 'Minimal project skeleton with a single welcome page',
        'mvc'       => 'Full MVC CRUD scaffold with user management',
        'blog'      => 'Blog with posts, categories and pagination',
        'api'       => 'RESTful API with authentication boilerplate',
    ];

    private const DRIVERS = [
        'SQLite'      => 'sqlite',
        'MySQL'       => 'mysql',
        'PostgreSQL'  => 'pgsql',
        'SQL Server'  => 'sqlsrv',
    ];

    private const DRIVER_PORTS = [
        'sqlite' => '',
        'mysql'  => '3306',
        'pgsql'  => '5432',
        'sqlsrv' => '1433',
    ];

    private ConsoleStyle $style;

    public function __construct()
    {
        $this->style = new ConsoleStyle();
    }

    /**
     * @param string[] $options
     */
    public function handle(string $name = '', array $options = []): void
    {
        if (str_starts_with($name, '--')) {
            $options[] = $name;
            $name = '';
        }

        $opts = $this->parseOptions($options);

        // Non-interactive when any explicit option is given, OR when a project
        // name is passed positionally (e.g. `php zero new my-app`). Otherwise
        // fall back to the guided wizard.
        $interactive = !$this->hasAnyOption($opts) && $name === '';

        if ($interactive) {
            $answers = $this->wizard();
        } else {
            $answers = $this->fromOptions($name, $opts);
        }

        if ($answers === null) {
            return; // user aborted the wizard
        }

        $this->summary($answers);
        $this->scaffold($answers);
    }

    /**
     * @param array<string,mixed> $opts
     * @return array<string,mixed>|null
     */
    private function wizard(): ?array
    {
        $this->style->writeln(Banner::header(App::VERSION));
        $this->style->writeln('');
        $this->style->writeln('<fg=white>Welcome to </><fg=cyan;options=bold>ZeroPing Framework!</>');
        $this->style->writeln('');

        $projectName = (new Prompt('Project Name', 'My App'))->prompt();
        $type        = (new ChoicePrompt('Project Type', array_keys(self::TEMPLATES), 1))->prompt();
        $driverLabel = (new ChoicePrompt('Database Driver', array_keys(self::DRIVERS), 0))->prompt();
        $auth        = (new ConfirmPrompt('Install Authentication?', true))->prompt();
        $tailwind    = (new ConfirmPrompt('Install Tailwind Starter?', true))->prompt();
        $crud        = (new ConfirmPrompt('Install Example CRUD?', true))->prompt();
        $location    = (new Prompt('Project Location', './' . $this->slugify($projectName)))->prompt();

        $driver = self::DRIVERS[$driverLabel] ?? 'sqlite';

        $answers = [
            'name'     => $projectName,
            'type'     => strtolower($type),
            'driver'   => $driver,
            'driverLabel' => $driverLabel,
            'auth'     => $auth,
            'tailwind' => $tailwind,
            'crud'     => $crud,
            'location' => $location,
        ];

        if (!(new ConfirmPrompt('Generate project?', true))->prompt()) {
            $this->style->writeln('');
            $this->style->writeln('<fg=yellow>Cancelled. No files were created.</>');
            return null;
        }

        return $answers;
    }

    /**
     * @param array<string,mixed> $opts
     * @return array<string,mixed>
     */
    private function fromOptions(string $name, array $opts): array
    {
        $type = strtolower((string) ($opts['type'] ?? 'mvc'));
        if (!isset(self::TEMPLATES[$type])) {
            $type = 'mvc';
        }

        $driverLabel = 'SQLite';
        if (isset($opts['db'])) {
            $driverLabel = $this->driverLabelFromCode((string) $opts['db']);
        }

        $projectName = $opts['name'] ?? ($name !== '' ? $name : 'My App');

        $location = $opts['dir'] ?? ($opts['location'] ?? './' . $this->slugify((string) $projectName));

        return [
            'name'     => $projectName,
            'type'     => $type,
            'driver'   => self::DRIVERS[$driverLabel] ?? 'sqlite',
            'driverLabel' => $driverLabel,
            'auth'     => isset($opts['auth']),
            'tailwind' => isset($opts['tailwind']),
            'crud'     => isset($opts['crud']),
            'location' => $location,
        ];
    }

    /**
     * @param array<string,mixed> $a
     */
    private function summary(array $a): void
    {
        $this->style->writeln('');
        $this->style->writeln('<options=bold;fg=cyan>Project Summary</>');
        $this->style->writeln('<fg=gray>' . str_repeat('═', 40) . '</>');
        $this->style->writeln('');
        $this->style->writeln("  <fg=white>Project      :</> <fg=green>{$a['name']}</>");
        $this->style->writeln("  <fg=white>Template     :</> <fg=green>{$a['type']}</>");
        $this->style->writeln("  <fg=white>Database     :</> <fg=green>{$a['driverLabel']}</>");
        $this->style->writeln("  <fg=white>Auth         :</> <fg=green>" . ($a['auth'] ? 'Yes' : 'No') . '</>');
        $this->style->writeln("  <fg=white>Tailwind     :</> <fg=green>" . ($a['tailwind'] ? 'Yes' : 'No') . '</>');
        $this->style->writeln("  <fg=white>Example CRUD :</> <fg=green>" . ($a['crud'] ? 'Yes' : 'No') . '</>');
        $this->style->writeln("  <fg=white>Location     :</> <fg=green>{$a['location']}</>");
        $this->style->writeln('');
    }

    /**
     * @param array<string,mixed> $a
     */
    private function scaffold(array $a): void
    {
        $frameworkDir = getcwd();
        $targetDir = $this->resolveTarget((string) $a['location']);

        $this->style->writeln('');

        $steps = [
            'Creating directories'        => fn() => $this->ensureParent($targetDir),
            'Copying template'         => fn() => $this->copyAll($frameworkDir, $targetDir, $a),
            'Installing dependencies'   => fn() => $this->stubDependencies(),
            'Generating .env'          => fn() => $this->prepareEnv($targetDir, (string) $a['name']),
            'Creating app key'         => fn() => $this->ensureKey($targetDir),
            'Configuring database'     => fn() => $this->configureDatabase($targetDir, $a),
            'Personalizing project'    => fn() => $this->personalize($targetDir, $a),
            'Running migrations'       => fn() => $this->stubMigrations(),
            'Creating storage'         => fn() => $this->ensureStorage($targetDir),
            'Installing starter files' => fn() => $this->installStarterFiles($targetDir, $a),
        ];

        foreach ($steps as $label => $work) {
            $this->style->write("  <fg=cyan>⟳</> <fg=white>{$label}</> ...");
            try {
                $work();
                $this->style->writeln("\r  <fg=green>✔</> <fg=white>{$label}</>");
            } catch (\Throwable $e) {
                $this->style->writeln("\r  <fg=red>✗</> <fg=white>{$label}</> <fg=gray>( {$e->getMessage()} )</>");
            }
        }

        $renderer = new \App\Core\Console\InstallerSuccessRenderer(
            projectName: $a['name'],
            starterType: $a['type'],
            frameworkVersion: \App\Core\Application\App::VERSION,
            phpVersion: PHP_VERSION,
            projectPath: $targetDir,
        );

        echo "\n" . $renderer->render() . "\n";
    }

    // ── Scaffolding helpers ───────────────────────────────────────────────

    /**
     * @param array<string,mixed> $a
     */
    private function copyAll(string $source, string $target, array $a): void
    {
        $sourceDir = BASE_PATH . '/templates/' . $a['type'];

        if (!is_dir($sourceDir)) {
            throw new \RuntimeException("Unknown template '{$a['type']}'.");
        }

        if (is_dir($target)) {
            throw new \RuntimeException("Target directory already exists: {$target}");
        }

        $this->copyFramework($source, $target);
        $this->copyDirectory($sourceDir, $target);
    }

    /**
     * Whether the framework repository (not a generated app) is being run.
     * Detected by the presence of the top-level framework-site/ directory,
     * which is excluded from every generated project.
     */
    private function isFrameworkRepo(): bool
    {
        return is_dir(BASE_PATH . '/framework-site');
    }

    private function ensureParent(string $target): void
    {
        $parent = dirname($target);
        if (!is_dir($parent)) {
            mkdir($parent, 0755, true);
        }
        if (!is_dir($parent) || !is_writable($parent)) {
            throw new \RuntimeException("Cannot write to: {$parent}");
        }
    }

    private function copyFramework(string $source, string $target): void
    {
        $items = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($items as $item) {
            $relative = str_replace('\\', '/', $items->getSubPathname());

            if ($this->isExcluded($relative)) {
                continue;
            }

            $dest = $target . '/' . $relative;
            $sourcePath = $item->getPathname();

            if (is_dir($sourcePath) && !$item->isDir()) {
                continue;
            }

            if ($item->isDir()) {
                if (!is_dir($dest)) {
                    mkdir($dest, 0755, true);
                }
            } else {
                if (!is_dir(dirname($dest))) {
                    mkdir(dirname($dest), 0755, true);
                }
                if (!@copy($sourcePath, $dest)) {
                    $this->style->writeln("  <fg=yellow>skipped</> <fg=gray>{$relative}</>");
                }
            }
        }
    }

    private function isExcluded(string $relative): bool
    {
        $relative = str_replace('\\', '/', $relative);
        $top = explode('/', $relative)[0];

        $excludeTop = [
            '.git', '.github', '.idea', '.devcontainer', '.docker', '.opencode',
            '.phpunit.cache', 'vendor', 'node_modules', 'arena', 'docs', 'tests',
            'bench', 'packages', 'composer.lock', '.env', 'storage',
            'package.json', 'package-lock.json',
        ];

        if (in_array($top, $excludeTop, true)) {
            return true;
        }

        $excluded = [
            'templates',
            'framework-site',
            'docs/website',
            'views/site',
            'views/home',
            'views/components',
            'views/layouts/site.php',
        ];

        foreach ($excluded as $path) {
            if ($relative === $path || str_starts_with($relative, $path . '/')) {
                return true;
            }
        }

        return false;
    }

    private function copyDirectory(string $source, string $dest): void
    {
        if (!is_dir($source)) {
            return;
        }

        $items = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($items as $item) {
            $relative = str_replace('\\', '/', $items->getSubPathname());
            $target = $dest . '/' . $relative;

            if ($item->isDir()) {
                if (!is_dir($target)) {
                    mkdir($target, 0755, true);
                }
            } else {
                if (!is_dir(dirname($target))) {
                    mkdir(dirname($target), 0755, true);
                }

                copy($item->getRealPath(), $target);
            }
        }
    }

    /**
     * @param array<string,mixed> $a
     */
    private function personalize(string $targetDir, array $a): void
    {
        $generator = new ProjectMetadataGenerator(
            projectName: (string) $a['name'],
            starterType: (string) $a['type'],
            frameworkVersion: \App\Core\Application\App::VERSION,
            databaseDriver: (string) ($a['driver'] ?? 'sqlite'),
        );

        $generator->replaceInDirectory($targetDir);

        $envPath = $targetDir . '/.env';
        if (file_exists($envPath)) {
            $generator->brandEnv($envPath);
        }

        $composerPath = $targetDir . '/composer.json';
        if (file_exists($composerPath)) {
            $generator->brandComposerJson($composerPath);
        }

        $readmePath = $targetDir . '/README.md';
        $generator->generateReadme($readmePath);

        $publicDir = $targetDir . '/public';
        if (is_dir($publicDir)) {
            $generator->generateAssets($publicDir);
        }
    }

    private function prepareEnv(string $dir, string $projectName = 'ZeroPing'): void
    {
        $envPath = $dir . '/.env';
        $examplePath = $dir . '/.env.example';

        if (!file_exists($envPath) && file_exists($examplePath)) {
            copy($examplePath, $envPath);
        }

        if (!file_exists($envPath)) {
            return;
        }

        $env = (string) file_get_contents($envPath);
        $appName = preg_match('/\s/', $projectName) ? '"' . $projectName . '"' : $projectName;

        if (preg_match('/^APP_NAME=/m', $env)) {
            $env = preg_replace('/^APP_NAME=.*$/m', 'APP_NAME=' . $appName, $env);
        } else {
            $env = "APP_NAME={$appName}\n" . $env;
        }

        if (!preg_match('/^APP_KEY=/m', $env)) {
            $env .= "\nAPP_KEY=\n";
        }

        file_put_contents($envPath, $env);
    }

    private function ensureKey(string $dir): void
    {
        $envPath = $dir . '/.env';
        if (!file_exists($envPath)) {
            return;
        }

        $env = (string) file_get_contents($envPath);

        if (preg_match('/^APP_KEY=(.*)$/m', $env, $m) && $m[1] === '') {
            $key = 'base64:' . base64_encode(random_bytes(32));
            $env = preg_replace('/^APP_KEY=.*$/m', 'APP_KEY=' . $key, $env);
            file_put_contents($envPath, $env);
        }
    }

    /**
     * @param array<string,mixed> $a
     */
    private function configureDatabase(string $dir, array $a): void
    {
        $envPath = $dir . '/.env';
        if (!file_exists($envPath)) {
            return;
        }

        $env = (string) file_get_contents($envPath);
        $driver = (string) $a['driver'];
        $port = self::DRIVER_PORTS[$driver] ?? '';

        $env = preg_replace('/^DB_CONNECTION=.*$/m', 'DB_CONNECTION=' . $driver, $env);

        if ($driver !== 'sqlite') {
            $replacements = [
                'DB_HOST'     => '127.0.0.1',
                'DB_PORT'     => $port,
                'DB_DATABASE' => 'zeroping',
                'DB_USERNAME' => 'root',
                'DB_PASSWORD' => '',
            ];
            foreach ($replacements as $key => $value) {
                if (preg_match('/^' . $key . '=/m', $env)) {
                    $env = preg_replace('/^' . $key . '=.*$/m', $key . '=' . $value, $env);
                } else {
                    $env .= "\n{$key}={$value}";
                }
            }
        }

        // Record the wizard's starter choices so the generated app can react.
        $flags = [
            'ZEROPING_AUTH'         => $a['auth'] ? 'true' : 'false',
            'ZEROPING_TAILWIND'    => $a['tailwind'] ? 'true' : 'false',
            'ZEROPING_EXAMPLE_CRUD' => $a['crud'] ? 'true' : 'false',
        ];
        foreach ($flags as $key => $value) {
            if (preg_match('/^' . $key . '=/m', $env)) {
                $env = preg_replace('/^' . $key . '=.*$/m', $key . '=' . $value, $env);
            } else {
                $env .= "\n{$key}={$value}";
            }
        }

        file_put_contents($envPath, $env);
    }

    private function installStarterFiles(string $dir, array $a): void
    {
        if (!$a['tailwind']) {
            return;
        }

        $layout = $dir . '/views/layouts/app.php';
        if (!file_exists($layout)) {
            return;
        }

        $content = (string) file_get_contents($layout);
        $snippet = '<script src="https://cdn.tailwindcss.com"></script>';

        if (stripos($content, 'tailwindcss.com') !== false) {
            return;
        }

        if (stripos($content, '</head>') !== false) {
            $content = str_ireplace('</head>', $snippet . "\n</head>", $content);
        } elseif (stripos($content, '<body') !== false) {
            $content = preg_replace('/<body[^>]*>/', "$0\n    " . $snippet, $content, 1);
        } else {
            $content = $snippet . "\n" . $content;
        }

        file_put_contents($layout, $content);
    }

    private function ensureStorage(string $dir): void
    {
        foreach (['storage/cache', 'storage/logs', 'storage/framework/cache'] as $sub) {
            $path = $dir . '/' . $sub;
            if (!is_dir($path)) {
                mkdir($path, 0755, true);
            }
        }
    }

    private function stubDependencies(): void
    {
        // Dependencies are installed by the user with `composer install`
        // once the project is in place. We don't run it here so the
        // scaffolder never blocks on a network download.
    }

    private function stubMigrations(): void
    {
        // Migrations run after `composer install` brings in the
        // database drivers. Surfaced as a next step instead.
    }

    private function resolveTarget(string $location): string
    {
        $location = trim($location);
        if ($location === '') {
            $location = './my-app';
        }

        if (str_starts_with($location, './') || str_starts_with($location, '../')) {
            return getcwd() . '/' . ltrim($location, './');
        }

        if (str_starts_with($location, '/') || preg_match('/^[A-Za-z]:\\\\/', $location)) {
            return $location;
        }

        return getcwd() . '/' . $location;
    }

    // ── Option parsing ──────────────────────────────────────────────────

    /**
     * @param string[] $options
     * @return array<string,mixed>
     */
    private function parseOptions(array $options): array
    {
        $parsed = [];

        foreach ($options as $opt) {
            if (str_starts_with($opt, '--')) {
                $body = substr($opt, 2);
                if (str_contains($body, '=')) {
                    [$key, $value] = explode('=', $body, 2);
                    $parsed[$key] = $value;
                } else {
                    $parsed[$body] = true;
                }
            }
        }

        return $parsed;
    }

    /**
     * @param array<string,mixed> $opts
     */
    private function hasAnyOption(array $opts): bool
    {
        $known = ['type', 'db', 'auth', 'tailwind', 'crud', 'dir', 'location', 'name', 'force'];

        foreach ($known as $key) {
            if (isset($opts[$key])) {
                return true;
            }
        }

        return false;
    }

    private function driverLabelFromCode(string $code): string
    {
        $code = strtolower($code);
        foreach (self::DRIVERS as $label => $value) {
            if ($value === $code || strtolower($label) === $code) {
                return $label;
            }
        }
        return 'SQLite';
    }

    private function slugify(string $name): string
    {
        return strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', $name), '-'));
    }
}
