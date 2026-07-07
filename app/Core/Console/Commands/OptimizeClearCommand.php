<?php

namespace App\Core\Console\Commands;

use App\Core\Console\Command;

class OptimizeClearCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected string $signature = 'optimize:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected string $description = 'Remove the cached bootstrap files';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->call('config:clear');
        $this->call('route:clear');
        $this->call('view:clear');
    }
}
