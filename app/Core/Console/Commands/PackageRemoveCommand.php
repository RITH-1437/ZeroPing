<?php

declare(strict_types=1);

namespace App\Core\Console\Commands;

use App\Core\Console\Command;
use App\Core\Packages\PackageConfig;

class PackageRemoveCommand extends Command
{
    protected string $description = 'Remove a ZeroPing package';

    public function handle(string $name): void
    {
        if (empty($name)) {
            $this->warn('Usage: php zero package:remove <name> [--force]');

            return;
        }

        $config = new PackageConfig(BASE_PATH);
        $config->remove($name);

        $this->success("Package removed from config: {$name}");

        if ($this->option('force') !== null) {
            $dir = BASE_PATH . '/packages/' . str_replace('/', '/', $name);

            if (is_dir($dir)) {
                $this->removeDir($dir);
                $this->line("  <fg=gray>Deleted {$dir}</>");
            }
        }

        $this->line("  <fg=gray>Run `composer dump-autoload` to refresh discovery.</>");
    }

    private function removeDir(string $dir): void
    {
        $items = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($items as $item) {
            if ($item->isDir()) {
                rmdir($item->getRealPath());
            } else {
                unlink($item->getRealPath());
            }
        }

        rmdir($dir);
    }
}
