<?php

namespace App\Core\Console\Commands;

use App\Core\Console\Command;
use App\Core\Database\Database;
use Throwable;

class DbSeedCommand extends Command
{
    protected string $signature = 'db:seed';

    protected string $description = 'Seed the database with records';

    public function handle(): void
    {
        $directory = BASE_PATH . '/database/seeders';

        if (!is_dir($directory)) {
            $this->warn('No seeders directory found at database/seeders.');
            return;
        }

        $specific = $this->option('class');

        if ($specific !== null) {
            $seeders = $this->resolveSeeder($directory, $specific);

            if ($seeders === []) {
                $this->error("Seeder class \"{$specific}\" was not found in database/seeders.");
                return;
            }
        } else {
            $seeders = $this->discoverSeeders($directory);
        }

        if ($seeders === []) {
            $this->warn('No seeders found. Create one with: php zero make:seeder DemoSeeder');
            return;
        }

        $this->title('Database Seeding');

        try {
            Database::connect();
        } catch (Throwable $e) {
            $this->error('Could not connect to the database: ' . $e->getMessage());
            return;
        }

        $this->progress(count($seeders), function (int $index, int $total) use ($seeders) {
            $class = $seeders[$index];
            $this->line("  <fg=gray>›</> <fg=white>{$class}</>");
            (new $class())->run();
        }, 'Seeding');

        $this->success('Database seeded successfully (' . count($seeders) . ' seeder(s)).');
    }

    /**
     * @return string[]
     */
    private function discoverSeeders(string $directory): array
    {
        $seeders = [];

        foreach (glob($directory . '/*.php') ?: [] as $file) {
            $seeders = array_merge($seeders, $this->loadSeedersFromFile($file));
        }

        sort($seeders);

        return $seeders;
    }

    /**
     * @return string[]
     */
    private function resolveSeeder(string $directory, string $specific): array
    {
        $found = [];

        foreach (glob($directory . '/*.php') ?: [] as $file) {
            foreach ($this->loadSeedersFromFile($file) as $class) {
                if ($class === $specific || $class === "App\\Database\\Seeders\\{$specific}") {
                    $found[] = $class;
                }
            }
        }

        return $found;
    }

    /**
     * @return string[]
     */
    private function loadSeedersFromFile(string $file): array
    {
        $before = get_declared_classes();

        require_once $file;

        $after = get_declared_classes();

        $seeders = [];

        foreach (array_diff($after, $before) as $class) {
            if (str_starts_with($class, 'App\\Database\\Seeders\\') && method_exists($class, 'run')) {
                $seeders[] = $class;
            }
        }

        return $seeders;
    }
}
