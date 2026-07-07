<?php

namespace App\Core\Console\Commands;

use App\Core\Console\Command;
use App\Core\Testing\TestRunner;
use App\Core\Testing\TestSuite;

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
     * @return void
     */
    public function handle(): void
    {
        $this->info('Running tests...');

        $suite = new TestSuite();
        $runner = new TestRunner();

        $this->discoverTests($suite);

        $runner->run($suite);

        $this->info('Tests completed successfully!');
    }

    protected function discoverTests(TestSuite $suite): void
    {
        $this->discover('tests/Unit', $suite);
        $this->discover('tests/Feature', $suite);
        $this->discover('tests/Integration', $suite);
    }

    protected function discover(string $path, TestSuite $suite): void
    {
        $path = BASE_PATH . '/' . $path;

        if (!is_dir($path)) {
            return;
        }

        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path));

        foreach ($files as $file) {
            if ($file->isDir()) {
                continue;
            }

            if (substr($file->getFilename(), -8) === 'Test.php') {
                require_once $file->getPathname();
                $class = 'Tests\\' . str_replace('/', '\\', substr($file->getPathname(), strlen($path) + 1, -4));
                $suite->add(new $class());
            }
        }
    }
}
