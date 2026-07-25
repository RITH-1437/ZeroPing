<?php

namespace App\Core\Console\Commands;

use App\Core\Console\Command;

class TestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected string $signature = 'test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected string $description = 'Run the application tests';

    /**
     * Execute the console command.
     *
     * Delegates to PHPUnit, which is the runner used by the project's test
     * suite (configured via phpunit.xml).
     *
     * @return void
     */
    public function handle(): void
    {
        $this->info('Running tests...');

        $binary = BASE_PATH . '/vendor/bin/phpunit';
        if (PHP_OS_FAMILY === 'Windows' && !is_file($binary)) {
            $binary .= '.bat';
        }

        if (!is_file($binary)) {
            $this->error('PHPUnit binary not found. Run "composer install" first.');
            exit(1);
        }

        $config = BASE_PATH . '/phpunit.xml';

        $command = sprintf(
            '%s --configuration %s --no-coverage',
            escapeshellarg($binary),
            escapeshellarg($config)
        );

        $exitCode = 0;
        passthru($command, $exitCode);

        if ($exitCode !== 0) {
            $this->error('Tests failed.');
            exit($exitCode);
        }

        $this->info('Tests completed successfully!');
    }
}
