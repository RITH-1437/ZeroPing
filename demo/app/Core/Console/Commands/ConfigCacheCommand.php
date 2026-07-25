<?php

namespace App\Core\Console\Commands;

use App\Core\Console\Command;
use App\Core\Support\Config;

class ConfigCacheCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected string $signature = 'config:cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected string $description = 'Create a cache file for faster configuration loading';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $config = Config::all();
        $this->write(
            BASE_PATH . '/bootstrap/cache/config.php',
            '<?php return ' . var_export($config, true) . ';'
        );

        $this->info('Configuration cached successfully!');
    }
}
