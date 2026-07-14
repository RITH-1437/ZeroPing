<?php

namespace App\Core\Console\Commands;

use App\Core\Console\Banner;
use App\Core\Console\Command;
use App\Core\Application\App;

/**
 * `php zero about` — a snapshot of the framework and host environment.
 */
class AboutCommand extends Command
{
    protected string $signature = 'about';

    protected string $description = 'Show framework, PHP, environment and link information';

    public function handle(): void
    {
        $this->line(Banner::header(App::VERSION));
        $this->line('');

        $composer = $this->composerVersion();

        $rows = [
            ['Framework Version', 'v' . App::VERSION],
            ['PHP Version', PHP_VERSION],
            ['Composer Version', $composer],
            ['Environment', $this->value($_ENV['APP_ENV'] ?? 'local')],
            ['Database Driver', $this->value(strtoupper($this->config('database.default', env('DB_CONNECTION', 'sqlite'))))],
            ['Application Name', $this->config('app.name', env('APP_NAME', 'ZeroPing'))],
            ['Timezone', $this->config('app.timezone', env('APP_TIMEZONE', date_default_timezone_get()))],
            ['Cache Driver', $this->value($this->config('cache.default', 'file'))],
            ['Session Driver', $this->value($this->config('session.driver', 'file'))],
            ['Queue Driver', $this->value($this->config('queue.default', 'sync'))],
        ];

        $this->section('Environment');

        foreach ($rows as [$label, $value]) {
            $padded = str_pad($label, 20);
            $this->line('  <fg=cyan>' . $padded . '</> <fg=white>' . $value . '</>');
        }

        $this->section('Useful Links');

        $links = [
            ['Documentation', 'https://github.com/RITH-1437/ZeroPing/tree/main/docs'],
            ['GitHub', 'https://github.com/RITH-1437/ZeroPing'],
            ['Issues', 'https://github.com/RITH-1437/ZeroPing/issues'],
            ['License', 'MIT — https://github.com/RITH-1437/ZeroPing/blob/main/LICENSE'],
        ];

        foreach ($links as [$label, $url]) {
            $padded = str_pad($label, 20);
            $this->line('  <fg=cyan>' . $padded . '</> <fg=blue>' . $url . '</>');
        }

        $this->line('');
    }

    private function value(string $text): string
    {
        return $text === '' ? '<fg=gray>(not set)</>' : $text;
    }

    private function config(string $key, $default = null)
    {
        return function_exists('config') ? config($key, $default) : $default;
    }

    private function composerVersion(): string
    {
        $raw = @shell_exec('composer --version 2>&1') ?? '';

        if (preg_match('/(\d+\.\d+\.\d+)/', $raw, $m)) {
            return $m[1];
        }

        return '<fg=gray>(unavailable)</>';
    }
}
