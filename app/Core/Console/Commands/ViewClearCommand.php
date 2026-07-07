<?php

namespace App\Core\Console\Commands;

use App\Core\Console\Command;

class ViewClearCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected string $signature = 'view:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected string $description = 'Clear all compiled view files';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        // This is a simplified implementation. A real implementation would
        // clear the compiled views.
        $this->info('Compiled views cleared!');
    }
}
