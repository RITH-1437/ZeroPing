<?php

namespace App\Core\Console\Commands;

use App\Core\Cache\Cache;
use App\Core\Console\Command;

class CacheClearCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected string $signature = 'cache:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected string $description = 'Flush the application cache';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        Cache::flush();

        $this->info('Application cache cleared!');
    }
}
