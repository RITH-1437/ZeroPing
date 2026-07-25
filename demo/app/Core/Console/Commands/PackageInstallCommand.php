<?php

declare(strict_types=1);

namespace App\Core\Console\Commands;

use App\Core\Console\Command;
use App\Core\Packages\PackageConfig;
use App\Core\Packages\ProviderRepository;

class PackageInstallCommand extends Command
{
    protected string $description = 'Install and enable a ZeroPing package';

    public function handle(string $name): void
    {
        if (empty($name)) {
            $this->warn('Usage: php zero package:install <name>');

            return;
        }

        $repo = new ProviderRepository(BASE_PATH, BASE_PATH . '/bootstrap/cache/packages.php');
        $manifest = $repo->getCached() ?? $repo->buildManifest(
            (new PackageConfig(BASE_PATH))->all(),
            $this->autoDiscoverEnabled()
        );

        if (!isset($manifest[$name])) {
            $this->warn("Package {$name} not discovered locally. Attempting composer require...");

            $exit = $this->composer('require', [$name]);

            if ($exit !== 0) {
                $this->error("composer require failed (offline?). Add {$name} manually.");

                return;
            }
        }

        (new PackageConfig(BASE_PATH))->enable($name);

        $this->success("Package installed & enabled: {$name}");
    }

    private function composer(string $command, array $args): int
    {
        $cmd = 'composer ' . $command . ' ' . implode(' ', array_map('escapeshellarg', $args));

        exec($cmd, $output, $code);

        return $code;
    }
}
