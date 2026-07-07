<?php

namespace App\Core\Console\Commands;

use App\Core\Console\Command;

class ConfigClearCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected string $signature = 'config:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected string $description = 'Remove the configuration cache file';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $file = BASE_PATH . '/bootstrap/cache/config.php';

        if (file_exists($file)) {
            unlink($file);
        }

        $this->info('Configuration cache cleared!');
    }
}
