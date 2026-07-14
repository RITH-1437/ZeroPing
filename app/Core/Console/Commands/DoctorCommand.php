<?php

namespace App\Core\Console\Commands;

use App\Core\Console\Banner;
use App\Core\Console\Command;
use App\Core\Application\App;

class DoctorCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected string $signature = 'doctor';

    /**
     * The console command description.
     *
     * @var string
     */
    protected string $description = 'Verify the ZeroPing installation and environment';

    /**
     * @var string[]
     */
    private array $errors = [];

    /**
     * @var string[]
     */
    private array $warnings = [];

    private int $passed = 0;

    public function handle(): void
    {
        $this->line(Banner::header(App::VERSION));
        $this->line('');
        $this->line('<fg=gray>Diagnosing your installation and environment...</>');

        $this->section('Runtime');
        $this->checkPhpVersion();
        $this->checkExtensions();
        $this->checkComposer();

        $this->section('Application');
        $this->checkEnvFile();
        $this->checkAppKey();
        $this->checkConfig();

        $this->section('Filesystem');
        $this->checkDirectories();
        $this->checkStorage();
        $this->checkCache();

        $this->section('Services');
        $this->checkDatabase();

        $this->line('');
        $this->renderSummary();

        if (!empty($this->errors)) {
            exit(1);
        }
    }

    // ── Runtime checks ───────────────────────────────────────────────────────

    private function checkPhpVersion(): void
    {
        $version = PHP_VERSION;

        if (version_compare($version, '8.1.0', '>=')) {
            $this->pass('PHP version', "PHP {$version}");
            return;
        }

        $this->fail('PHP version', "PHP {$version} (requires >= 8.1)");
        $this->errors[] = 'Upgrade to PHP 8.1 or higher.';
    }

    private function checkExtensions(): void
    {
        $required = ['pdo', 'mbstring', 'json', 'ctype', 'tokenizer', 'fileinfo', 'openssl', 'hash'];
        $missing = [];

        foreach ($required as $ext) {
            if (!extension_loaded($ext)) {
                $missing[] = $ext;
            }
        }

        if (empty($missing)) {
            $this->pass('PHP extensions', implode(', ', $required));
        } else {
            $this->fail('PHP extensions', 'missing: ' . implode(', ', $missing));
            $this->errors[] = 'Enable these PHP extensions in php.ini: ' . implode(', ', $missing);
        }

        if (!extension_loaded('pdo')) {
            $this->fail('PDO', 'pdo extension not loaded');
            $this->errors[] = 'Enable the pdo extension to use any database.';
        } else {
            $this->pass('PDO', 'available');
        }
    }

    private function checkComposer(): void
    {
        $version = null;

        if (defined('Composer\\Composer::VERSION')) {
            $candidate = \Composer\Composer::VERSION;
            if ($candidate !== '' && $candidate !== '@package_version@') {
                $version = $candidate;
            }
        }

        if ($version === null) {
            $output = @shell_exec('composer --version 2>&1');
            if (is_string($output) && preg_match('/Composer(?: version)?\s+(\d+\.\d+\.\d+)/', $output, $m)) {
                $version = $m[1];
            }
        }

        if ($version === null) {
            $this->warnCheck('Composer', 'not detected on PATH');
            $this->warnings[] = 'Install Composer 2.x: https://getcomposer.org/download/';
            return;
        }

        if (version_compare($version, '2.0.0', '>=')) {
            $this->pass('Composer', "v{$version}");
        } else {
            $this->warnCheck('Composer', "v{$version} (2.x recommended)");
            $this->warnings[] = 'Upgrade Composer: composer self-update';
        }
    }

    // ── Application checks ────────────────────────────────────────────────────

    private function checkEnvFile(): void
    {
        if (file_exists($this->path('.env'))) {
            $this->pass('Environment file', '.env present');
            return;
        }

        if (file_exists($this->path('.env.example'))) {
            $this->fail('Environment file', '.env missing');
            $this->errors[] = 'Create your environment file: copy .env.example to .env';
        } else {
            $this->fail('Environment file', '.env and .env.example missing');
            $this->errors[] = 'Restore your .env file before running the application.';
        }
    }

    private function checkAppKey(): void
    {
        $key = $_ENV['APP_KEY'] ?? ($_SERVER['APP_KEY'] ?? getenv('APP_KEY'));

        if (!empty($key) && $key !== 'base64:') {
            $this->pass('Application key', 'set');
            return;
        }

        $this->warnCheck('Application key', 'not set');
        $this->warnings[] = 'Generate an application key: php zero key:generate';
    }

    private function checkConfig(): void
    {
        $configDir = $this->path('config');

        if (!is_dir($configDir)) {
            $this->fail('Configuration', 'config directory missing');
            $this->errors[] = 'The config directory is missing from your project.';
            return;
        }

        $required = ['app.php'];
        $missing = array_values(array_filter(
            $required,
            fn ($f) => !file_exists($configDir . '/' . $f)
        ));

        if ($missing !== []) {
            $this->fail('Configuration', 'missing: ' . implode(', ', $missing));
            $this->errors[] = 'Restore missing config files: ' . implode(', ', $missing);
            return;
        }

        $this->pass('Configuration', count(glob($configDir . '/*.php')) . ' file(s) loaded');
    }

    // ── Filesystem checks ─────────────────────────────────────────────────────

    private function checkDirectories(): void
    {
        $dirs = ['storage', 'storage/cache', 'storage/logs', 'bootstrap/cache'];
        $failed = [];

        foreach ($dirs as $dir) {
            $path = $this->path($dir);

            if (!is_dir($path)) {
                @mkdir($path, 0775, true);
            }

            if (!is_dir($path) || !is_writable($path)) {
                $failed[] = $dir;
            }
        }

        if ($failed === []) {
            $this->pass('Runtime directories', 'writable');
        } else {
            $this->fail('Runtime directories', 'not writable: ' . implode(', ', $failed));
            foreach ($failed as $dir) {
                $this->errors[] = "Make directory writable: {$dir}";
            }
        }
    }

    private function checkStorage(): void
    {
        $dir = $this->path('storage');
        $probe = $dir . '/.doctor-probe';

        if (@file_put_contents($probe, 'ok') !== false) {
            @unlink($probe);
            $this->pass('Storage', 'writable');
        } else {
            $this->fail('Storage', 'not writable');
            $this->errors[] = 'Grant write permission to the storage directory.';
        }
    }

    private function checkCache(): void
    {
        $dir = $this->path('bootstrap/cache');
        $probe = $dir . '/.doctor-probe';

        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }

        if (@file_put_contents($probe, 'ok') !== false) {
            @unlink($probe);
            $this->pass('Cache', 'writable');
        } else {
            $this->fail('Cache', 'not writable');
            $this->errors[] = 'Grant write permission to bootstrap/cache.';
        }
    }

    // ── Service checks ─────────────────────────────────────────────────────────

    /**
     * @var array<string, array{pdo: string, dsn: string, charset: string}>
     */
    private const DATABASE_DRIVERS = [
        'sqlite'  => ['pdo' => 'pdo_sqlite',  'dsn' => 'sqlite',            'charset' => 'utf8'],
        'mysql'   => ['pdo' => 'pdo_mysql',   'dsn' => 'mysql',             'charset' => 'utf8mb4'],
        'mariadb' => ['pdo' => 'pdo_mysql',   'dsn' => 'mysql',             'charset' => 'utf8mb4'],
        'pgsql'   => ['pdo' => 'pdo_pgsql',   'dsn' => 'pgsql',             'charset' => 'utf8'],
    ];

    private function checkDatabase(): void
    {
        $connection = strtolower((string) ($_ENV['DB_CONNECTION'] ?? ($_SERVER['DB_CONNECTION'] ?? 'sqlite')));
        $host = $_ENV['DB_HOST'] ?? ($_SERVER['DB_HOST'] ?? '127.0.0.1');
        $port = $_ENV['DB_PORT'] ?? ($_SERVER['DB_PORT'] ?? (
            $connection === 'pgsql' ? 5432 : 3306
        ));
        $name = $_ENV['DB_NAME'] ?? ($_SERVER['DB_NAME'] ?? '');
        $user = $_ENV['DB_USER'] ?? ($_SERVER['DB_USER'] ?? 'root');
        $pass = $_ENV['DB_PASS'] ?? ($_SERVER['DB_PASS'] ?? '');

        if (!array_key_exists($connection, self::DATABASE_DRIVERS)) {
            $this->fail('Database driver', "unknown driver '{$connection}'");
            $this->errors[] = 'Set DB_CONNECTION to one of: ' . implode(', ', array_keys(self::DATABASE_DRIVERS));
            return;
        }

        $driver = self::DATABASE_DRIVERS[$connection];
        $this->pass('Database driver', $connection);

        // Only diagnose the configured driver's PDO extension.
        if (!extension_loaded($driver['pdo'])) {
            $this->fail('PDO extension', "{$driver['pdo']} not loaded");
            $this->errors[] = "Enable the {$driver['pdo']} PHP extension to use {$connection}.";
            return;
        }

        if ($connection === 'sqlite') {
            $database = $name !== '' ? $name : $this->path('database/database.sqlite');

            if (!file_exists($database)) {
                $this->warnCheck('SQLite database', 'file does not exist yet');
                $this->warnings[] = "The SQLite file '{$database}' will be created on first connection (run: php zero migrate).";
                return;
            }

            $this->pass('SQLite database', $database);

            try {
                $pdo = new \PDO('sqlite:' . $database);
            } catch (\Throwable $e) {
                $this->warnCheck('Database connection', 'unavailable');
                $this->warnings[] = 'Could not open the SQLite database: ' . $e->getMessage();
                return;
            }

            $this->pass('Database connection', 'connected');
            $this->checkMigrations($pdo);
            return;
        }

        // MySQL / MariaDB / PostgreSQL
        $this->pass('Database credentials', "{$user}@{$host}:{$port}/{$name}");

        try {
            $pdo = new \PDO(
                sprintf(
                    '%s:host=%s;port=%s;charset=%s',
                    $driver['dsn'],
                    $host,
                    $port,
                    $driver['charset']
                ),
                $user,
                (string) $pass,
                [\PDO::ATTR_TIMEOUT => 3]
            );
        } catch (\Throwable $e) {
            $this->warnCheck('Database connection', 'unavailable');
            $this->warnings[] = 'Could not connect to the database. Check DB_HOST/DB_PORT/DB_USER/DB_PASS in .env.';
            return;
        }

        $this->pass('Database connection', 'connected');
        $this->checkMigrations($pdo);
    }

    /**
     * Report migration status against an already-open connection.
     *
     * A single "Migrations" line is emitted so the output can never show both
     * "file missing" and "connected", or contradict itself.
     */
    private function checkMigrations(\PDO $pdo): void
    {
        $idColumn = match ($pdo->getAttribute(\PDO::ATTR_DRIVER_NAME)) {
            'sqlite'                                => 'id INTEGER PRIMARY KEY AUTOINCREMENT',
            'mysql'                                => 'id INT AUTO_INCREMENT PRIMARY KEY',
            'pgsql'                                => 'id SERIAL PRIMARY KEY',
            default                                => 'id INT PRIMARY KEY',
        };

        try {
            $pdo->exec(
                'CREATE TABLE IF NOT EXISTS migrations ('
                . $idColumn . ', '
                . 'migration VARCHAR(255) NOT NULL, '
                . 'batch INTEGER NOT NULL, '
                . 'created_at TIMESTAMP)'
            );
            $count = (int) $pdo->query('SELECT COUNT(*) FROM migrations')->fetchColumn();
        } catch (\Throwable $e) {
            $this->warnCheck('Migrations', 'status unknown');
            $this->warnings[] = 'Could not read the migrations table: ' . $e->getMessage();
            return;
        }

        if ($count === 0) {
            $this->warnCheck('Migrations', 'none have been run yet');
            $this->warnings[] = 'Run your database migrations: php zero migrate';
        } else {
            $this->pass('Migrations', $count . ' migration(s) applied');
        }
    }

    // ── Reporting ──────────────────────────────────────────────────────────────

    private function pass(string $label, string $detail): void
    {
        $this->passed++;
        $badge = '<options=bold;fg=black;bg=green> PASS </>';
        $this->line("  {$badge} <fg=white>{$label}</> <fg=gray>— {$detail}</>");
    }

    private function warnCheck(string $label, string $detail): void
    {
        $badge = '<options=bold;fg=black;bg=yellow> WARN </>';
        $this->line("  {$badge} <fg=white>{$label}</> <fg=gray>— {$detail}</>");
    }

    private function fail(string $label, string $detail): void
    {
        $badge = '<options=bold;fg=white;bg=red> FAIL </>';
        $this->line("  {$badge} <fg=white>{$label}</> <fg=gray>— {$detail}</>");
    }

    private function renderSummary(): void
    {
        $this->output->writeln('<fg=gray>' . str_repeat('─', 60) . '</>');

        $summary = sprintf(
            '  <fg=green>%d passed</>  <fg=yellow>%d warning(s)</>  <fg=red>%d error(s)</>',
            $this->passed,
            count($this->warnings),
            count($this->errors)
        );
        $this->line($summary);
        $this->line('');

        if (empty($this->errors) && empty($this->warnings)) {
            $this->success('All checks passed. ZeroPing is ready to use.');
            return;
        }

        if (!empty($this->warnings)) {
            $this->section('Recommendations');
            foreach ($this->warnings as $warning) {
                $this->line("  <fg=yellow>•</> <fg=white>{$warning}</>");
            }
        }

        if (!empty($this->errors)) {
            $this->section(count($this->errors) . ' issue(s) need attention');
            foreach ($this->errors as $error) {
                $this->line("  <fg=red>•</> <fg=white>{$error}</>");
            }
        }
    }

    private function path(string $relative): string
    {
        $base = defined('BASE_PATH') ? rtrim((string) BASE_PATH, '/\\') : getcwd();
        return $base . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relative);
    }
}
