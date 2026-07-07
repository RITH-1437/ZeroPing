<?php

namespace App\Core\Console\Commands;

use App\Core\Cache\Cache;
use App\Core\Console\Command;

class CacheTestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected string $signature = 'cache:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected string $description = 'Test the cache system';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->info('Testing cache system...');

        $this->testPut();
        $this->testGet();
        $this->testRemember();
        $this->testHas();
        $this->testForget();
        $this->testFlush();
        $this->testExpiration();

        $this->info('Cache system test completed successfully!');
    }

    protected function testPut(): void
    {
        Cache::put('test', 'value', 60);
        $this->assert(Cache::get('test') === 'value', 'put');
    }

    protected function testGet(): void
    {
        Cache::put('test', 'value', 60);
        $this->assert(Cache::get('test') === 'value', 'get');
    }

    protected function testRemember(): void
    {
        $value = Cache::remember('test', 60, function () {
            return 'remembered';
        });
        $this->assert($value === 'remembered', 'remember');
    }

    protected function testHas(): void
    {
        Cache::put('test', 'value', 60);
        $this->assert(Cache::has('test'), 'has');
    }

    protected function testForget(): void
    {
        Cache::put('test', 'value', 60);
        Cache::forget('test');
        $this->assert(!Cache::has('test'), 'forget');
    }

    protected function testFlush(): void
    {
        Cache::put('test1', 'value1', 60);
        Cache::put('test2', 'value2', 60);
        Cache::flush();
        $this->assert(!Cache::has('test1') && !Cache::has('test2'), 'flush');
    }

    protected function testExpiration(): void
    {
        Cache::put('test', 'value', 1);
        sleep(2);
        $this->assert(Cache::get('test') === null, 'expiration');
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
