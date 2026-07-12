<?php

namespace App\Core\Console\Commands;

use App\Core\Console\Command;

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

    public function handle(): void
    {
        $this->title('ZeroPing Installation Doctor');
        $this->line('');

        $this->checkPhpVersion();
        $this->checkExtensions();
        $this->checkEnv();
        $this->checkDirectories();
        $this->checkAppKey();
        $this->checkDatabase();

        $this->line('');
        $this->renderSummary();

        if (!empty($this->errors)) {
            exit(1);
        }
    }

    private function checkPhpVersion(): void
    {
        $version = PHP_VERSION;
        $ok = version_compare($version, '8.1.0', '>=');

        $this->report($ok, 'PHP version', "PHP {$version}");

        if (!$ok) {
            $this->errors[] = 'PHP 8.1 or higher is required.';
        }
    }

    private function checkExtensions(): void
    {
        $required = ['pdo', 'pdo_mysql', 'mbstring', 'json', 'ctype', 'tokenizer', 'fileinfo', 'openssl', 'hash'];
        $missing = [];

        foreach ($required as $ext) {
            if (!extension_loaded($ext)) {
                $missing[] = $ext;
            }
        }

        if (empty($missing)) {
            $this->report(true, 'PHP extensions', implode(', ', $required));
        } else {
            $this->report(false, 'PHP extensions', 'missing: ' . implode(', ', $missing));
            $this->errors[] = 'Missing PHP extensions: ' . implode(', ', $missing);
        }
    }

    private function checkEnv(): void
    {
        if (file_exists('.env')) {
            $this->report(true, '.env file', 'present');
            return;
        }

        $this->report(false, '.env file', 'missing (copy .env.example to .env)');
        $this->errors[] = 'The .env file is missing. Run: cp .env.example .env';
    }

    private function checkDirectories(): void
    {
        $dirs = ['storage/cache', 'storage/logs', 'bootstrap/cache'];
        $ok = true;

        foreach ($dirs as $dir) {
            if (!is_dir($dir)) {
                @mkdir($dir, 0777, true);
                $this->line("  <fg=yellow>created</> <fg=gray>{$dir}</>");
            }

            if (!is_writable($dir)) {
                $ok = false;
                $this->errors[] = "Directory is not writable: {$dir}";
            }
        }

        if ($ok) {
            $this->report(true, 'Runtime directories', 'writable');
        } else {
            $this->report(false, 'Runtime directories', 'not writable');
        }
    }

    private function checkAppKey(): void
    {
        $key = $_ENV['APP_KEY'] ?? ($_SERVER['APP_KEY'] ?? getenv('APP_KEY'));

        if (!empty($key) && $key !== 'base64:') {
            $this->report(true, 'Application key', 'set');
            return;
        }

        $this->report(false, 'Application key', 'not set (run: php zero key:generate)');
        $this->warnings[] = 'APP_KEY is not set. Run: php zero key:generate';
    }

    private function checkDatabase(): void
    {
        $host = $_ENV['DB_HOST'] ?? ($_SERVER['DB_HOST'] ?? '127.0.0.1');
        $port = $_ENV['DB_PORT'] ?? ($_SERVER['DB_PORT'] ?? 3306);
        $user = $_ENV['DB_USER'] ?? ($_SERVER['DB_USER'] ?? 'root');
        $pass = $_ENV['DB_PASS'] ?? ($_SERVER['DB_PASS'] ?? '');

        try {
            $pdo = new \PDO(
                "mysql:host={$host};port={$port};charset=utf8mb4",
                $user,
                $pass
            );
            $this->report(true, 'Database connection', 'ok');
        } catch (\Throwable $e) {
            $this->report(false, 'Database connection', 'could not connect');
            $this->warnings[] = 'Could not connect to the database: ' . $e->getMessage();
        }
    }

    private function report(bool $ok, string $label, string $detail): void
    {
        $mark = $ok ? '<fg=green>✔</>' : '<fg=red>✗</>';
        $this->line("  {$mark} <fg=white>{$label}</> <fg=gray>— {$detail}</>");
    }

    private function renderSummary(): void
    {
        if (empty($this->errors) && empty($this->warnings)) {
            $this->success('All checks passed. ZeroPing is ready to use.');
            return;
        }

        foreach ($this->warnings as $warning) {
            $this->warn($warning);
        }

        if (!empty($this->errors)) {
            $this->error(count($this->errors) . ' blocking issue(s) found:');
            foreach ($this->errors as $error) {
                $this->line("  <fg=red>•</> <fg=white>{$error}</>");
            }
        }
    }
}
