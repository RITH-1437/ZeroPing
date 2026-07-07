<?php

namespace App\Core\Console\Commands;

use App\Core\Console\Command;

class OptimizeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected string $signature = 'optimize';

    /**
     * The console command description.
     *
     * @var string
     */
    protected string $description = 'Cache the framework bootstrap files';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->call('config:cache');
        $this->call('route:cache');
        $this->call('view:cache');
    }
}
