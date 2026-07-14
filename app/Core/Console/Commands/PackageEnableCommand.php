<?php

declare(strict_types=1);

namespace App\Core\Console\Commands;

use App\Core\Console\Command;
use App\Core\Packages\PackageConfig;
use App\Core\Packages\ProviderRepository;

class PackageEnableCommand extends Command
{
    protected string $description = 'Enable a ZeroPing package';

    public function handle(string $name): void
    {
        if (empty($name)) {
            $this->warn('Usage: php zero package:enable <name>');

            return;
        }

        (new PackageConfig(BASE_PATH))->enable($name);
        $this->refreshManifest();

        $this->success("Package enabled: {$name}");
        $this->line("  <fg=gray>It will now boot on the next `php zero` / request.</>");
    }

    private function refreshManifest(): void
    {
        $repo = new ProviderRepository(BASE_PATH, BASE_PATH . '/bootstrap/cache/packages.php');
        $enabled = (new PackageConfig(BASE_PATH))->all();

        $repo->cache($repo->buildManifest($enabled, $this->autoDiscoverEnabled()));
    }
}
