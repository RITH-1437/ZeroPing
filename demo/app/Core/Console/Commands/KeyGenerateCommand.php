<?php

namespace App\Core\Console\Commands;

use App\Core\Console\Command;
use App\Core\Security\Random;

class KeyGenerateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected string $signature = 'key:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected string $description = 'Set the application key';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $key = 'base64:' . base64_encode(Random::string(32));

        $this->updateEnvFile($key);

        $this->info("Application key [{$key}] set successfully.");
    }

    protected function updateEnvFile(string $key): void
    {
        $env = file_get_contents('.env');

        if (preg_match('/^APP_KEY=.*$/m', $env)) {
            $env = preg_replace('/^APP_KEY=.*$/m', 'APP_KEY=' . $key, $env);
        } else {
            $env = rtrim($env) . PHP_EOL . 'APP_KEY=' . $key . PHP_EOL;
        }

        file_put_contents('.env', $env);

        // Clear cached env so subsequent commands pick up the change
        foreach (glob(BASE_PATH . '/bootstrap/cache/env_*.php') as $cache) {
            @unlink($cache);
        }
    }
}
