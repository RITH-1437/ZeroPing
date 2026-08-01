<?php

declare(strict_types=1);

namespace App\Core\Console\Commands;

use App\Core\Console\Command;
use App\Core\Console\ConsoleStyle;

class SecurityAuditCommand extends Command
{
    protected string $signature = 'security:audit';
    protected string $description = 'Run a security audit of the application configuration';

    private ConsoleStyle $style;
    private int $warnings = 0;
    private int $failures = 0;

    public function __construct()
    {
        $this->style = new ConsoleStyle();
    }

    /** @param string[] $options */
    public function handle(array $options = []): int
    {
        $this->style->writeln('');
        $this->style->writeln('<fg=cyan;options=bold>ZeroPing Security Audit</>');
        $this->style->writeln('<fg=gray>Checking application security configuration...</>');
        $this->style->writeln('');

        $this->checkAppKey();
        $this->checkDebugMode();
        $this->checkEnvironment();
        $this->checkSessionConfig();
        $this->checkCorsConfig();
        $this->checkHttpsConfig();
        $this->checkFilePermissions();
        $this->checkEnvFileExposure();

        $this->style->writeln('');
        $this->style->writeln(str_repeat('-', 60));

        if ($this->failures === 0 && $this->warnings === 0) {
            $this->style->writeln('<fg=green>All security checks passed.</>');
            return 0;
        }

        $this->style->writeln(
            sprintf(
                '  <fg=red>%d failure(s)</> · <fg=yellow>%d warning(s)</>',
                $this->failures,
                $this->warnings
            )
        );

        return $this->failures > 0 ? 1 : 0;
    }

    private function checkAppKey(): void
    {
        $key = config('security.key', '');
        if (empty($key) || $key === 'base64:') {
            $this->fail('APP_KEY', 'Application key is not set. Run: php zero key:generate');
        } elseif (strlen(base64_decode(substr($key, 7)) ?: '') < 32) {
            $this->warnCheck('APP_KEY', 'Application key appears to be shorter than 32 bytes.');
        } else {
            $this->pass('APP_KEY', 'Application key is set and appears valid.');
        }
    }

    private function checkDebugMode(): void
    {
        $debug = config('app.debug', false);
        $env   = config('app.env', 'local');

        if ($debug && in_array($env, ['production', 'staging'], true)) {
            $this->fail('Debug mode', 'APP_DEBUG is true in ' . $env . ' environment — disable for production.');
        } elseif ($debug) {
            $this->warnCheck('Debug mode', 'APP_DEBUG is enabled (environment: ' . $env . ').');
        } else {
            $this->pass('Debug mode', 'Debug mode is disabled.');
        }
    }

    private function checkEnvironment(): void
    {
        $env = config('app.env', 'local');
        if ($env === 'local' || $env === 'development') {
            $this->warnCheck('Environment', 'APP_ENV is ' . $env . '. Set to production before deploying.');
        } else {
            $this->pass('Environment', 'APP_ENV is ' . $env . '.');
        }
    }

    private function checkSessionConfig(): void
    {
        $lifetime = config('session.lifetime', 120);
        if ((int)$lifetime > 480) {
            $this->warnCheck('Session lifetime', 'Session lifetime is ' . $lifetime . ' minutes. Consider reducing for security.');
        } else {
            $this->pass('Session lifetime', 'Session lifetime is ' . $lifetime . ' minutes.');
        }

        $secure = config('session.secure', false);
        $env    = config('app.env', 'local');
        if (!$secure && in_array($env, ['production', 'staging'], true)) {
            $this->warnCheck('Session cookies', 'SESSION_SECURE_COOKIE is not enabled for production.');
        } else {
            $this->pass('Session cookies', 'Session cookie security settings OK.');
        }
    }

    private function checkCorsConfig(): void
    {
        $origins = config('cors.allowed_origins', ['*']);
        if (in_array('*', (array)$origins, true)) {
            $this->warnCheck('CORS', 'CORS allows all origins (*). Restrict to known domains in production.');
        } else {
            $this->pass('CORS', 'CORS is restricted to specific origins.');
        }
    }

    private function checkHttpsConfig(): void
    {
        $hsts = config('security.headers.hsts', '');
        if (empty($hsts)) {
            $this->warnCheck('HSTS', 'Strict-Transport-Security header is not configured.');
        } else {
            $this->pass('HSTS', 'HSTS header is configured.');
        }
    }

    private function checkFilePermissions(): void
    {
        $storageWritable = is_writable(base_path('storage'));
        $bootstrapWritable = is_writable(base_path('bootstrap/cache'));

        if (!$storageWritable) {
            $this->fail('File permissions', 'storage/ directory is not writable.');
        } else {
            $this->pass('File permissions', 'storage/ is writable.');
        }

        if (!$bootstrapWritable) {
            $this->warnCheck('File permissions', 'bootstrap/cache/ directory is not writable.');
        }
    }

    private function checkEnvFileExposure(): void
    {
        $publicEnv = public_path('.env');
        if (file_exists($publicEnv)) {
            $this->fail('.env exposure', '.env file found in public/ directory — remove immediately!');
        } else {
            $this->pass('.env exposure', '.env file is not publicly accessible.');
        }
    }

    private function pass(string $check, string $message): void
    {
        $this->style->writeln(
            '  <fg=black;bg=green> PASS </> <fg=white>' . $check . '</> <fg=gray>— ' . $message . '</>'
        );
    }

    private function warnCheck(string $check, string $message): void
    {
        $this->warnings++;
        $this->style->writeln(
            '  <fg=black;bg=yellow> WARN </> <fg=white>' . $check . '</> <fg=gray>— ' . $message . '</>'
        );
    }

    private function fail(string $check, string $message): void
    {
        $this->failures++;
        $this->style->writeln(
            '  <fg=white;bg=red> FAIL </> <fg=white>' . $check . '</> <fg=gray>— ' . $message . '</>'
        );
    }
}