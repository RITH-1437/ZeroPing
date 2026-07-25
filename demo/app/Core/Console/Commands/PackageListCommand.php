<?php

declare(strict_types=1);

namespace App\Core\Console\Commands;

use App\Core\Config\Config;
use App\Core\Console\Command;
use App\Core\Packages\ProviderRepository;

class PackageListCommand extends Command
{
    protected string $description = 'List installed ZeroPing packages';

    public function handle(): void
    {
        $repo = new ProviderRepository(
            BASE_PATH,
            BASE_PATH . '/bootstrap/cache/packages.php'
        );

        $enabledConfig = Config::has('packages')
            ? Config::get('packages', [])
            : [];

        $manifest = $repo->getCached()
            ?? $repo->buildManifest($enabledConfig, $this->autoDiscover());

        if (empty($manifest)) {
            $this->info('No ZeroPing packages installed.');

            return;
        }

        $this->title('Installed Packages');

        $enabled = [];
        $disabled = [];

        foreach ($manifest as $name => $pkg) {
            if ($pkg['enabled'] ?? true) {
                $enabled[] = $name;
            } else {
                $disabled[] = $name;
            }
        }

        if ($enabled !== []) {
            $this->section('Enabled');
            foreach ($enabled as $name) {
                $this->line("  <fg=green>✔ {$name}</>");
            }
        }

        if ($disabled !== []) {
            $this->section('Disabled');
            foreach ($disabled as $name) {
                $this->line("  <fg=yellow>{$name}</>");
            }
        }

        $this->line('');
    }

    private function autoDiscover(): bool
    {
        $flag = $_ENV['PACKAGE_AUTO_DISCOVER'] ?? getenv('PACKAGE_AUTO_DISCOVER') ?? 'true';

        return $flag !== 'false' && $flag !== '0';
    }
}
