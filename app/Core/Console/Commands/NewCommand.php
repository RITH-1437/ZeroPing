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
        'starter'   => 'Clean starter app with sample controller, model and routes',
        'empty'     => 'Minimal project skeleton with a single welcome page',
        'mvc'       => 'Full MVC CRUD scaffold with user management',
        'blog'      => 'Blog with posts, categories and pagination',
        'api'       => 'RESTful API with authentication boilerplate',
    ];

    private array $templates = [
        'starter'   => 'Clean starter app with sample controller, model and routes',
        'empty'     => 'Minimal project skeleton with a single welcome page',
        'mvc'       => 'Full MVC CRUD scaffold with user management',
        'blog'      => 'Blog with posts, categories and pagination',
        'api'       => 'RESTful API with authentication boilerplate',
    ];

    /**
     * Default template chosen by the wizard / when no --type is passed.
     */
    private const DEFAULT_TEMPLATE = 'starter';

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

    /** Whether Composer was detected on the PATH. */
    private bool $composerAvailable = true;

    /** Whether a network connection is available. */
    private bool $internetOk = true;

    /**
     * Files/dirs created inside the target during scaffolding. Used to roll
     * back a partially-created project if a step throws.
     *
     * @var array<int,string>
     */
    private array $createdItems = [];

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

        // Pre-flight environment checks (friendly errors, no stack traces).
        if (!$this->validateName((string) $answers['name'])) {
            return;
        }
        if (!$this->validateComposer()) {
            return;
        }
        if (!$this->validateInternet()) {
            return;
        }

        $this->summary($answers);

        try {
            $this->scaffold($answers);
        } catch (\Throwable $e) {
            $this->style->writeln('');
            $this->style->writeln("<fg=red>✗ Project creation failed: {$e->getMessage()}</>");
            $this->style->writeln('<fg=gray>A partial project may have been created. Re-run the command once the issue is resolved.</>');
        }
    }

    /**
     * Validate a project/folder name: non-empty, valid directory name, not a
     * reserved name, and no shell-hostile characters.
     */
    private function validateName(string $name): bool
    {
        $name = trim($name);

        if ($name === '') {
            $this->style->writeln('<fg=red>✗ Project name cannot be empty.</>');
            return false;
        }

        if (str_starts_with($name, '-')) {
            $this->style->writeln("<fg=red>✗ Project name cannot start with a hyphen: {$name}</>");
            return false;
        }

        if (in_array(strtolower($name), ['.', '..', '/', '\\'], true)) {
            $this->style->writeln("<fg=red>✗ Reserved name not allowed: {$name}</>");
            return false;
        }

        if (preg_match('/[\/\\\\:*?"<>|]/', $name)) {
            $this->style->writeln("<fg=red>✗ Project name contains invalid characters: {$name}</>");
            $this->style->writeln('<fg=gray>Allowed: letters, numbers, spaces and dashes/underscores.</>');
            return false;
        }

        return true;
    }

    /**
     * Verify Composer is available on the PATH (friendly error if missing).
     */
    private function validateComposer(): bool
    {
        $output = @shell_exec(escapeshellcmd($this->composerBin()) . ' --version 2>&1');

        if ($output === null || trim($output) === '' || stripos($output, 'composer') === false) {
            $this->style->writeln('<fg=yellow>⚠ Composer was not detected on your PATH.</>');
            $this->style->writeln('<fg=gray>Dependencies will be skipped — run `composer install` inside the project once available.</>');
            $this->composerAvailable = false;
        } else {
            $this->composerAvailable = true;
        }

        return true;
    }

    /**
     * Detect a working network connection to the repository host before
     * attempting any network step. Non-fatal: the scaffolder works offline
     * but asks the user to run composer manually afterwards.
     */
    private function validateInternet(): bool
    {
        $ok = false;

        if (function_exists('fsockopen')) {
            $conn = @fsockopen('github.com', 443, $errno, $errstr, 3);
            if ($conn !== false) {
                fclose($conn);
                $ok = true;
            }
        }

        if (!$ok && extension_loaded('curl')) {
            $ch = curl_init('https://github.com');
            curl_setopt_array($ch, [CURLOPT_TIMEOUT => 3, CURLOPT_NOBODY => true, CURLOPT_SSL_VERIFYPEER => false]);
            $ok = (bool) @curl_exec($ch);
            curl_close($ch);
        }

        $this->internetOk = $ok;

        if (!$ok) {
            $this->style->writeln('<fg=yellow>⚠ No internet connection detected.</>');
            $this->style->writeln('<fg=gray>Dependencies will be skipped — run `composer install` inside the project once online.</>');
        }

        return true;
    }

    private function composerBin(): string
    {
        return PHP_OS_FAMILY === 'Windows' ? 'composer.bat' : 'composer';
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
        $type        = (new ChoicePrompt('Project Type', array_keys(self::TEMPLATES), 0))->prompt();
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
        $type = strtolower((string) ($opts['type'] ?? self::DEFAULT_TEMPLATE));
        if (!isset(self::TEMPLATES[$type])) {
            $type = self::DEFAULT_TEMPLATE;
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

        $this->createdItems = [];

        // Friendly guards before touching the filesystem.
        if (is_dir($targetDir)) {
            $this->style->writeln("<fg=red>✗ Target directory already exists: {$targetDir}</>");
            $this->style->writeln('<fg=gray>Choose a different name or remove the existing directory first.</>');
            return;
        }

        $parent = dirname($targetDir);
        if (!is_writable($parent)) {
            $this->style->writeln("<fg=red>✗ Cannot write to: {$parent}</>");
            $this->style->writeln('<fg=gray>Check the directory permissions and try again.</>');
            return;
        }

        $this->style->writeln('');
        $this->style->writeln('<fg=cyan>Creating ZeroPing project...</>');

        $steps = [];

        $steps['Downloading starter'] = function () use ($frameworkDir, $targetDir, $a) {
            $this->copyAll($frameworkDir, $targetDir, $a);
        };

        $steps['Installing dependencies'] = function () use ($targetDir) {
            $this->runComposerInstall($targetDir);
        };

        $steps['Creating environment'] = function () use ($targetDir, $a) {
            $this->prepareEnv($targetDir, (string) $a['name']);
            $this->configureDatabase($targetDir, $a);
            $this->personalize($targetDir, $a);
        };

        $steps['Generating application key'] = function () use ($targetDir) {
            $this->ensureKey($targetDir);
        };

        $steps['Preparing storage'] = function () use ($targetDir) {
            $this->ensureStorage($targetDir);
        };

        $steps['Optimizing framework'] = function () use ($targetDir) {
            $this->optimizeFramework($targetDir);
        };

        $failed = false;
        $failedLabel = '';
        foreach ($steps as $label => $work) {
            $this->style->write("  <fg=cyan>⟳</> <fg=white>{$label}</> ...");
            try {
                $work();
                $this->style->writeln("\r  <fg=green>✔</> <fg=white>{$label}</>");
            } catch (\Throwable $e) {
                $this->style->writeln("\r  <fg=red>✗</> <fg=white>{$label}</> <fg=gray>( {$e->getMessage()} )</>");
                $failed = true;
                $failedLabel = $label;
                break;
            }
        }

        if ($failed) {
            $this->style->writeln('');
            $this->style->writeln("<fg=red>✗ Failed at step: {$failedLabel}</>");
            $this->rollbackDir($targetDir);
            $this->style->writeln("<fg=yellow>↺ Rolled back the partially-created project.</>");
            $this->style->writeln('<fg=gray>No changes were left on disk. Resolve the issue above and try again.</>');
            return;
        }

        $this->style->writeln('');
        $this->style->writeln('<fg=green>Project created successfully.</>');

        $renderer = new \App\Core\Console\InstallerSuccessRenderer(
            projectName: $a['name'],
            starterType: $a['type'],
            frameworkVersion: \App\Core\Application\App::VERSION,
            phpVersion: PHP_VERSION,
            projectPath: $targetDir,
        );

        echo "\n" . $renderer->render() . "\n";
    }

    /**
     * Remove a partially-created target directory (rollback helper).
     */
    private function rollbackDir(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        $items = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($items as $item) {
            if ($item->isDir()) {
                @rmdir($item->getRealPath());
            } else {
                @unlink($item->getRealPath());
            }
        }

        @rmdir($dir);
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
                    $this->createdItems[] = $dest;
                }
            } else {
                if (!is_dir(dirname($dest))) {
                    mkdir(dirname($dest), 0755, true);
                }
                if (!@copy($sourcePath, $dest)) {
                    $this->style->writeln("  <fg=yellow>skipped</> <fg=gray>{$relative}</>");
                } else {
                    $this->createdItems[] = $dest;
                }
            }
        }
    }

    /**
     * Decide whether a repo file should be copied into a generated app.
     *
     * A generated application must contain ONLY application files — never the
     * framework repository internals (CI/CD, Docker, website, docs, dev
     * tooling, roadmap, etc.). We use an allow-list approach: anything that
     * is not part of the application skeleton is excluded.
     */
    private function isExcluded(string $relative): bool
    {
        $relative = str_replace('\\', '/', $relative);
        $top = explode('/', $relative)[0];

        // Application skeleton directories that ARE copied.
        $allowTop = [
            'app', 'bootstrap', 'config', 'database', 'public',
            'resources', 'routes', 'storage', 'tests', 'views',
        ];

        if (in_array($top, $allowTop, true)) {
            return $this->isExcludedAppFile($relative);
        }

        // Specific application-level files that ARE copied.
        $allowFiles = [
            '.env.example',
            '.gitignore',
            'composer.json',
            'zero',
            'README.md',
        ];

        if (in_array($relative, $allowFiles, true)) {
            return false;
        }

        // Everything else (framework repo internals) is excluded.
        return true;
    }

    /**
     * Within the allowed app/ tree, hide framework-repo demo controllers,
     * website views and any other non-application artifacts.
     */
    private function isExcludedAppFile(string $relative): bool
    {
        $excluded = [
            'app/Controllers/CoffeeController.php',
            'app/Controllers/HomeController.php',
            'app/Controllers/TestController.php',
            'app/Controllers/UserController.php',
            'views/site',
            'views/home',
            'views/components',
            'views/layouts/site.php',
            'public/sitemap.xml',
            'public/robots.txt',
            'public/assets/images/og-image.svg',
            'public/assets/images/mascot.svg',
            'public/assets/images/app-icon.svg',
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
                    $this->createdItems[] = $target;
                }
            } else {
                if (!is_dir(dirname($target))) {
                    mkdir(dirname($target), 0755, true);
                }

                copy($item->getRealPath(), $target);
                $this->createdItems[] = $target;
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

    /**
     * Install Composer dependencies in the generated project. Skipped (with a
     * note) when Composer is missing or there is no network connection.
     */
    private function runComposerInstall(string $dir): void
    {
        if (!$this->composerAvailable || !$this->internetOk) {
            $this->style->writeln('');
            $this->style->writeln('<fg=yellow>  ⓘ Skipped — run `composer install` inside the project.</>');
            return;
        }

        $result = @shell_exec('cd ' . escapeshellarg($dir) . ' && ' . escapeshellcmd($this->composerBin()) . ' install --no-interaction --no-progress 2>&1');

        if ($result === null || stripos($result, 'error') !== false && stripos($result, 'Generated') === false) {
            $this->style->writeln('');
            $this->style->writeln('<fg=yellow>  ⓘ Composer install encountered an issue — run it manually.</>');
        }
    }

    /**
     * Optimize the framework autoloader / caches in the generated project.
     */
    private function optimizeFramework(string $dir): void
    {
        if (!$this->composerAvailable) {
            return;
        }

        @shell_exec('cd ' . escapeshellarg($dir) . ' && ' . escapeshellcmd($this->composerBin()) . ' dump-autoload --optimize --no-interaction 2>&1');
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
