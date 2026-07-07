<?php

namespace App\Core\Console\Commands;

use App\Core\Console\Command;
use App\Core\Cache\Cache;

class QueueRestartCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected string $signature = 'queue:restart';

    /**
     * The console command description.
     *
     * @var string
     */
    protected string $description = 'Restart queue worker daemons after their current job';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        Cache::forever('zeroping:queue:restart', time());

        $this->info('Broadcasting queue restart signal.');
    }
}
