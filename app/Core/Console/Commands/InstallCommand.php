<?php

namespace App\Core\Console\Commands;

use App\Core\Console\Command;
use App\Core\Security\Random;

/**
 * Interactive installation wizard.
 *
 * Guides a developer through configuring a fresh ZeroPing application:
 * environment, database, application name, key generation, migrations and
 * an optional starter template. Errors are always reported as friendly,
 * actionable messages — never raw stack traces.
 */
class InstallCommand extends Command
{
    protected string $signature = 'install';

    protected string $description = 'Run the interactive ZeroPing installation wizard';

    /**
     * @var array<string, string>
     */
    private array $env = [];

    public function handle(): void
    {
        try {
            $this->welcome();
            $this->ensureEnvFile();
            $this->configureEnvironment();
            $this->configureDatabase();
            $this->generateKey();
            $this->runMigrations();
            $this->installTemplate();
            $this->finish();
        } catch (\Throwable $e) {
            $this->line('');
            $this->error('Installation could not be completed.');
            $this->line('  <fg=gray>' . $e->getMessage() . '</>');
            $this->line('  <fg=gray>Run</> <fg=cyan>php zero doctor</> <fg=gray>to diagnose your environment.</>');
            exit(1);
        }
    }

    // ── Steps ──────────────────────────────────────────────────────────────────

    private function welcome(): void
    {
        $this->output->writeln('');
        $this->output->writeln("<fg=cyan>  ███████╗███████╗██████╗  ██████╗ ██████╗ ██╗███╗   ██╗ ██████╗</>");
        $this->output->writeln("<fg=cyan>  ╚══███╔╝██╔════╝██╔══██╗██╔═══██╗██╔══██╗██║████╗  ██║██╔════╝</>");
        $this->output->writeln("<fg=cyan>    ███╔╝ █████╗  ██████╔╝██║   ██║██████╔╝██║██╔██╗ ██║██║  ███╗</>");
        $this->output->writeln("<fg=cyan>   ███╔╝  ██╔══╝  ██╔══██╗██║   ██║██╔═══╝ ██║██║╚██╗██║██║   ██║</>");
        $this->output->writeln("<fg=cyan>  ███████╗███████╗██║  ██║╚██████╔╝██║     ██║██║ ╚████║╚██████╔╝</>");
        $this->output->writeln("<fg=cyan>  ╚══════╝╚══════╝╚═╝  ╚═╝ ╚═════╝ ╚═╝     ╚═╝╚═╝  ╚═══╝ ╚═════╝</>");
        $this->output->writeln('');
        $this->output->writeln('  <options=bold;fg=white>Welcome to the ZeroPing installation wizard</> <fg=yellow>v' . \App\Core\Application\App::VERSION . '</>');
        $this->output->writeln('  <fg=gray>This will guide you through configuring your application.</>');
        $this->output->writeln('  <fg=gray>Press Enter to accept the [default] shown for each question.</>');
        $this->output->writeln('');

        if (!$this->confirm('Ready to begin?', true)) {
            $this->line('<fg=yellow>Installation cancelled.</>');
            exit(0);
        }
    }

    private function ensureEnvFile(): void
    {
        $envPath = $this->path('.env');
        $examplePath = $this->path('.env.example');

        if (!file_exists($envPath)) {
            if (!file_exists($examplePath)) {
                throw new \RuntimeException('.env.example is missing; cannot create .env.');
            }

            if (!@copy($examplePath, $envPath)) {
                throw new \RuntimeException('Could not create .env (permission denied).');
            }
        }

        $this->env = $this->readEnv($envPath);
    }

    private function configureEnvironment(): void
    {
        $this->step('Environment');

        $name = $this->ask('Application name', $this->env['APP_NAME'] ?? 'ZeroPing');
        $this->env['APP_NAME'] = $this->quoteIfNeeded($name);

        $environment = $this->choice(
            'Application environment',
            ['local', 'production'],
            0
        );
        $this->env['APP_ENV'] = $environment;

        $debug = $environment === 'local'
            ? $this->confirm('Enable debug mode?', true)
            : $this->confirm('Enable debug mode?', false);
        $this->env['APP_DEBUG'] = $debug ? 'true' : 'false';

        $url = $this->ask('Application URL', $this->env['APP_URL'] ?? 'http://localhost:1437');
        $this->env['APP_URL'] = $url;

        $this->writeEnv();
        $this->ok('Environment configured.');
    }

    private function configureDatabase(): void
    {
        $this->step('Database');

        if (!$this->confirm('Configure the database now?', true)) {
            $this->skip('Skipped — configure DB_* values in .env when ready.');
            return;
        }

        $driver = $this->choice('Database driver', ['mysql', 'sqlite'], 0);
        $this->env['DB_CONNECTION'] = $driver;

        if ($driver === 'sqlite') {
            $database = $this->ask('SQLite database path', $this->env['DB_NAME'] ?? 'database/database.sqlite');
            $this->env['DB_NAME'] = $database;

            $full = $this->path($database);
            if (!file_exists($full)) {
                @mkdir(dirname($full), 0775, true);
                @touch($full);
            }
        } else {
            $this->env['DB_HOST'] = $this->ask('Database host', $this->env['DB_HOST'] ?? '127.0.0.1');
            $this->env['DB_PORT'] = $this->askNumeric('Database port', $this->env['DB_PORT'] ?? '3306');
            $this->env['DB_NAME'] = $this->ask('Database name', $this->env['DB_NAME'] ?? 'zero_ping');
            $this->env['DB_USER'] = $this->ask('Database user', $this->env['DB_USER'] ?? 'root');
            $this->env['DB_PASS'] = $this->secret('Database password (leave blank for none)');
        }

        $this->writeEnv();
        $this->testDatabase();
    }

    private function testDatabase(): void
    {
        try {
            if (($this->env['DB_CONNECTION'] ?? 'mysql') === 'sqlite') {
                new \PDO('sqlite:' . $this->path($this->env['DB_NAME'] ?? 'database/database.sqlite'));
            } else {
                new \PDO(
                    sprintf(
                        'mysql:host=%s;port=%s;charset=utf8mb4',
                        $this->env['DB_HOST'] ?? '127.0.0.1',
                        $this->env['DB_PORT'] ?? '3306'
                    ),
                    $this->env['DB_USER'] ?? 'root',
                    (string) ($this->env['DB_PASS'] ?? ''),
                    [\PDO::ATTR_TIMEOUT => 3]
                );
            }
            $this->ok('Database connection successful.');
        } catch (\Throwable $e) {
            $this->warn('Could not connect to the database.');
            $this->line('  <fg=gray>' . $e->getMessage() . '</>');
            $this->line('  <fg=gray>You can fix DB_* values in .env later.</>');
        }
    }

    private function generateKey(): void
    {
        $this->step('Application Key');

        $current = $this->env['APP_KEY'] ?? '';

        if ($current !== '' && $current !== 'base64:' && !$this->confirm('An application key already exists. Regenerate?', false)) {
            $this->skip('Kept existing application key.');
            return;
        }

        $this->env['APP_KEY'] = 'base64:' . base64_encode(Random::string(32));
        $this->writeEnv();
        $this->ok('Application key generated.');
    }

    private function runMigrations(): void
    {
        $this->step('Migrations');

        if (($this->env['DB_CONNECTION'] ?? '') === '' && ($this->env['DB_NAME'] ?? '') === '') {
            $this->skip('No database configured — skipping migrations.');
            return;
        }

        if (!$this->confirm('Run database migrations now?', true)) {
            $this->skip('Skipped — run "php zero migrate" later.');
            return;
        }

        try {
            $this->call('migrate');
            $this->ok('Migrations complete.');
        } catch (\Throwable $e) {
            $this->warn('Migrations could not be run: ' . $e->getMessage());
            $this->line('  <fg=gray>Fix your database configuration and run</> <fg=cyan>php zero migrate</>');
        }
    }

    private function installTemplate(): void
    {
        $this->step('Starter Template');

        if (!$this->confirm('Install a starter template into this project?', false)) {
            $this->skip('Keeping the current application skeleton.');
            return;
        }

        $templatesDir = dirname(__DIR__) . '/Templates';
        $available = array_values(array_filter(
            ['empty', 'mvc', 'blog', 'api'],
            static fn ($t) => is_dir($templatesDir . '/' . $t)
        ));

        if ($available === []) {
            $this->skip('No starter templates found.');
            return;
        }

        $type = $this->choice('Choose a starter template', $available, 0);
        $source = $templatesDir . '/' . $type;

        $this->warn("This may overwrite existing files in app/, config/ and views/.");
        if (!$this->confirm('Continue?', false)) {
            $this->skip('Template installation cancelled.');
            return;
        }

        $projectName = trim($this->env['APP_NAME'] ?? 'ZeroPing', '"');
        $this->overlayTemplate($source, $this->basePath(), $projectName, $type);
        $this->ok("Installed the '{$type}' starter template.");
    }

    private function finish(): void
    {
        $this->output->writeln('');
        $this->output->writeln('  <options=bold;fg=black;bg=green> SUCCESS </> <options=bold;fg=white>ZeroPing is installed and ready!</>');
        $this->output->writeln('');
        $this->output->writeln('  <fg=yellow>Start the development server:</>');
        $this->output->writeln('    <fg=green>$</> <fg=white>php zero serve</>');
        $this->output->writeln('');
        $this->output->writeln('  <fg=gray>Then open</> <fg=cyan>' . ($this->env['APP_URL'] ?? 'http://localhost:1437') . '</> <fg=gray>in your browser.</>');
        $this->output->writeln('  <fg=gray>Verify anytime with</> <fg=cyan>php zero doctor</>');
        $this->output->writeln('');
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    private function step(string $title): void
    {
        $this->output->writeln('');
        $this->output->writeln("  <options=bold;fg=cyan>▸ {$title}</>");
        $this->output->writeln('  <fg=gray>' . str_repeat('─', 40) . '</>');
    }

    private function ok(string $message): void
    {
        $this->output->writeln("  <fg=green>✔</> <fg=white>{$message}</>");
    }

    private function skip(string $message): void
    {
        $this->output->writeln("  <fg=gray>○ {$message}</>");
    }

    private function askNumeric(string $question, string $default): string
    {
        while (true) {
            $answer = $this->ask($question, $default);
            if (ctype_digit((string) $answer)) {
                return (string) $answer;
            }
            $this->warn('Please enter a valid number.');
        }
    }

    /**
     * @return array<string, string>
     */
    private function readEnv(string $path): array
    {
        $result = [];
        $content = (string) file_get_contents($path);

        foreach (preg_split('/\r\n|\r|\n/', $content) as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
                continue;
            }
            [$key, $value] = explode('=', $line, 2);
            $result[trim($key)] = trim($value);
        }

        return $result;
    }

    private function writeEnv(): void
    {
        $path = $this->path('.env');
        $content = (string) file_get_contents($path);

        foreach ($this->env as $key => $value) {
            $line = $key . '=' . $value;
            if (preg_match('/^' . preg_quote($key, '/') . '=.*$/m', $content)) {
                $content = preg_replace('/^' . preg_quote($key, '/') . '=.*$/m', $line, $content);
            } else {
                $content = rtrim($content) . "\n" . $line . "\n";
            }
        }

        if (@file_put_contents($path, $content) === false) {
            throw new \RuntimeException('Could not write to .env (permission denied).');
        }

        // Invalidate any cached env so subsequent steps see the new values.
        $_ENV = array_merge($_ENV, $this->env);
        foreach (glob($this->path('bootstrap/cache/env_*.php')) ?: [] as $cache) {
            @unlink($cache);
        }
    }

    private function quoteIfNeeded(string $value): string
    {
        if ($value === '' || preg_match('/\s/', $value)) {
            return '"' . str_replace('"', '\\"', trim($value, '"')) . '"';
        }
        return $value;
    }

    private function overlayTemplate(string $source, string $target, string $projectName, string $type): void
    {
        $items = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($items as $item) {
            $relative = $items->getSubPathname();
            $dest = $target . DIRECTORY_SEPARATOR . $relative;

            if ($item->isDir()) {
                if (!is_dir($dest)) {
                    @mkdir($dest, 0775, true);
                }
                continue;
            }

            $content = (string) file_get_contents($item->getRealPath());
            $content = str_replace(
                ['{{ project_name }}', '{{ project_type }}', '{{ project_slug }}', '{{ vendor }}', '{{ php_version }}'],
                [$projectName, strtoupper($type), $this->slugify($projectName), 'zeroping', PHP_VERSION],
                $content
            );

            if (!is_dir(dirname($dest))) {
                @mkdir(dirname($dest), 0775, true);
            }
            @file_put_contents($dest, $content);
        }
    }

    private function slugify(string $name): string
    {
        return strtolower(trim((string) preg_replace('/[^a-zA-Z0-9]+/', '-', $name), '-'));
    }

    private function basePath(): string
    {
        return defined('BASE_PATH') ? rtrim((string) BASE_PATH, '/\\') : (string) getcwd();
    }

    private function path(string $relative): string
    {
        return $this->basePath() . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relative);
    }
}
