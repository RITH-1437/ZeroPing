<?php

namespace App\Core\Console\Commands;

use App\Core\Console\Command;
use App\Core\View\View;

class ViewClearCommand extends Command
{
    protected string $signature = 'view:clear';
    protected string $description = 'Clear all compiled view files';

    public function handle(): void
    {
        $cacheDir = View::cachePath();

        if (!is_dir($cacheDir)) {
            $this->info('No cached views to clear.');
            return;
        }

        $files = glob($cacheDir . '/*.php');
        $count = 0;

        if ($files !== false) {
            foreach ($files as $file) {
                unlink($file);
                $count++;
            }
        }

        $this->info("View cache cleared! ({$count} files removed)");
    }
}
