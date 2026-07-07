<?php

namespace App\Core\Console\Commands;

use App\Core\Console\Command;
use App\Core\Support\Config;

class ConfigTestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected string $signature = 'config:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected string $description = 'Test the configuration loader';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->info('Testing configuration loader...');

        $this->assert(Config::get('app.name') === 'ZeroPing', 'app.name');
        $this->assert(Config::get('database.default') === 'mysql', 'database.default');

        $this->info('Configuration loader test completed successfully!');
    }

    protected function assert(bool $condition, string $test): void
    {
        if ($condition) {
            $this->info("✔ {$test}");
        } else {
            $this->error("✗ {$test}");
        }
    }
}
