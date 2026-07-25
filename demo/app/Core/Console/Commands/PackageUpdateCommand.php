<?php

declare(strict_types=1);

namespace App\Core\Console\Commands;

use App\Core\Console\Command;

class PackageUpdateCommand extends Command
{
    protected string $description = 'Update a ZeroPing package';

    public function handle(string $name): void
    {
        if (empty($name)) {
            $this->warn('Usage: php zero package:update <name>');

            return;
        }

        $cmd = 'composer update ' . escapeshellarg($name);

        exec($cmd, $output, $code);

        if ($code === 0) {
            $this->success("Package updated: {$name}");
        } else {
            $this->warn("composer update failed (offline?). Update {$name} manually.");
        }
    }
}
