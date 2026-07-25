<?php

namespace App\Core\Console\Commands;

use App\Core\Console\Command;
use App\Core\Filesystem\Storage;

class StorageClearCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected string $signature = 'storage:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected string $description = 'Clear the storage directory';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $disks = ['local', 'public'];

        foreach ($disks as $disk) {
            $files = Storage::disk($disk)->files();
            foreach ($files as $file) {
                Storage::disk($disk)->delete($file);
            }

            $directories = Storage::disk($disk)->directories();
            foreach ($directories as $directory) {
                Storage::disk($disk)->deleteDirectory($directory);
            }
        }

        $this->info('Storage cleared!');
    }
}
