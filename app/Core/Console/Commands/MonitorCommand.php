<?php

namespace App\Core\Console\Commands;

use App\Core\Application\App;
use App\Core\Console\Command;

/**
 * Application health / status report.
 *
 * Surfaces the framework version, PHP/runtime, and the status of the
 * core services (cache, queue, database, logging) so operators can sanity
 * check a ZeroPing install from the CLI.
 */
class MonitorCommand extends Command
{
    protected string $description = 'Show application health and service status';

    public function handle(): void
    {
        $this->title('ZeroPing Monitor');

        $this->line('  <fg=cyan>Version </> <fg=white>' . App::VERSION . '</>');
        $this->line('  <fg=cyan>PHP     </> <fg=white>' . PHP_VERSION . '</>');
        $this->line('  <fg=cyan>Env     </> <fg=white>' . (config('app.env') ?? 'local') . '</>');
        $this->line('  <fg=cyan>Debug   </> <fg=white>' . (config('app.debug') ? 'on' : 'off') . '</>');

        $this->section('Services');

        $this->service('Cache', fn () => config('cache.default') ?? 'none');
        $this->service('Queue', fn () => config('queue.default') ?? 'none');
        $this->service('Database', fn () => $this->dbDriver());
        $this->service('Mail', fn () => config('mail.default') ?? 'none');
        $this->service('Logging', fn () => $this->errorCount() . ' error(s) logged');

        $this->line('');
        $this->success('Monitor complete.');
    }

    private function service(string $label, callable $probe): void
    {
        try {
            $value = $probe();
        } catch (\Throwable $e) {
            $value = 'n/a';
        }

        $padded = str_pad($label, 10);
        $this->line('  <fg=cyan>' . $padded . '</> <fg=white>' . $value . '</>');
    }

    private function dbDriver(): string
    {
        return App::container()->make(\App\Core\Database\Database::class)->getDriverName();
    }

    private function errorCount(): int
    {
        $file = BASE_PATH . '/storage/logs/zeroping.log';

        if (!file_exists($file)) {
            return 0;
        }

        $count = 0;

        foreach (file($file) as $line) {
            if (str_contains($line, 'ERROR')) {
                $count++;
            }
        }

        return $count;
    }
}
