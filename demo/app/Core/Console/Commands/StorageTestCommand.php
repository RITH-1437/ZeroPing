<?php

namespace App\Core\Console\Commands;

use App\Core\Console\Command;
use App\Core\Filesystem\Storage;

class StorageTestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected string $signature = 'storage:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected string $description = 'Test the filesystem';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->info('Testing filesystem...');

        $this->testPut();
        $this->testGet();
        $this->testExists();
        $this->testDelete();
        $this->testCopy();
        $this->testMove();
        $this->testFiles();
        $this->testDirectories();
        $this->testMakeDirectory();
        $this->testDeleteDirectory();

        $this->info('Filesystem test completed successfully!');
    }

    protected function testPut(): void
    {
        Storage::put('test.txt', 'Hello World');
        $this->assert(Storage::exists('test.txt'), 'put');
    }

    protected function testGet(): void
    {
        Storage::put('test.txt', 'Hello World');
        $this->assert(Storage::get('test.txt') === 'Hello World', 'get');
    }

    protected function testExists(): void
    {
        Storage::put('test.txt', 'Hello World');
        $this->assert(Storage::exists('test.txt'), 'exists');
    }

    protected function testDelete(): void
    {
        Storage::put('test.txt', 'Hello World');
        Storage::delete('test.txt');
        $this->assert(!Storage::exists('test.txt'), 'delete');
    }

    protected function testCopy(): void
    {
        Storage::put('test.txt', 'Hello World');
        Storage::copy('test.txt', 'test2.txt');
        $this->assert(Storage::exists('test2.txt'), 'copy');
    }

    protected function testMove(): void
    {
        Storage::put('test.txt', 'Hello World');
        Storage::move('test.txt', 'test3.txt');
        $this->assert(Storage::exists('test3.txt') && !Storage::exists('test.txt'), 'move');
    }

    protected function testFiles(): void
    {
        Storage::put('test.txt', 'Hello World');
        $files = Storage::files();
        $this->assert(in_array('test.txt', $files), 'files');
    }

    protected function testDirectories(): void
    {
        Storage::makeDirectory('test');
        $directories = Storage::directories();
        $this->assert(in_array('test', $directories), 'directories');
    }

    protected function testMakeDirectory(): void
    {
        Storage::makeDirectory('test');
        $this->assert(Storage::exists('test'), 'makeDirectory');
    }

    protected function testDeleteDirectory(): void
    {
        Storage::makeDirectory('test');
        Storage::deleteDirectory('test');
        $this->assert(!Storage::exists('test'), 'deleteDirectory');
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
