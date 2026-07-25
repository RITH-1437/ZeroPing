<?php

declare(strict_types=1);

namespace App\Core\Console\Commands;

use App\Core\Console\Command;
use App\Core\Packages\PackageConfig;
use App\Core\Packages\ProviderRepository;

class PackageDisableCommand extends Command
{
    protected string $description = 'Disable a ZeroPing package';

    public function handle(string $name): void
    {
        if (empty($name)) {
            $this->warn('Usage: php zero package:disable <name>');

            return;
        }

        (new PackageConfig(BASE_PATH))->disable($name);
        $this->refreshManifest();

        $this->success("Package disabled: {$name}");
    }

    private function refreshManifest(): void
    {
        $repo = new ProviderRepository(BASE_PATH, BASE_PATH . '/bootstrap/cache/packages.php');
        $enabled = (new PackageConfig(BASE_PATH))->all();

        $repo->cache($repo->buildManifest($enabled, $this->autoDiscoverEnabled()));
    }
}
