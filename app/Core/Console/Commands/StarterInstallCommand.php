<?php

declare(strict_types=1);

namespace App\Core\Console\Commands;

use App\Core\Console\Command;
use App\Core\Packages\PackageConfig;
use App\Core\Packages\StarterKit;

class StarterInstallCommand extends Command
{
    protected string $description = 'Install a ZeroPing starter kit (bundle of packages)';

    public function handle(string $kit): void
    {
        if (empty($kit) || !StarterKit::exists($kit)) {
            $available = implode(', ', StarterKit::names());

            $this->warn('Usage: php zero starter:install <' . ($kit ?: 'kit') . '>');
            $this->line("  <fg=gray>Available: {$available}</>");

            return;
        }

        $definition = StarterKit::kits()[$kit];
        $config = new PackageConfig(BASE_PATH);

        $this->title("Installing starter: {$definition['label']}");

        foreach ($definition['packages'] as $package) {
            $config->enable($package);
            $this->task("Enabling {$package}", static fn () => null);
        }

        $this->success("Starter installed: {$kit}");

        foreach ($definition['notes'] as $note) {
            $this->line("  <fg=green>✔ {$note}</>");
        }

        $this->line('');
        $this->line("  <fg=gray>Run `composer dump-autoload` then `php zero migrate`.</>");
    }
}
