<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\Config\Config;
use App\Core\Config\ConfigRepository;

class ConfigTest extends \Tests\TestCase
{
    protected ConfigRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new ConfigRepository();
        Config::setRepository($this->repository);
    }

    public function testGetReturnsValueForExistingKey(): void
    {
        $this->repository->set(['app' => ['name' => 'ZeroPing']]);

        $this->assertSame('ZeroPing', Config::get('app.name'));
    }

    public function testGetReturnsDefaultForMissingKey(): void
    {
        $this->repository->set([]);

        $this->assertSame('fallback', Config::get('missing.key', 'fallback'));
    }

    public function testGetReturnsNullForMissingKeyWithNoDefault(): void
    {
        $this->repository->set([]);

        $this->assertNull(Config::get('missing.key'));
    }

    public function testHasReturnsTrueForExistingKey(): void
    {
        $this->repository->set(['debug' => true]);

        $this->assertTrue(Config::has('debug'));
    }

    public function testHasReturnsFalseForMissingKey(): void
    {
        $this->repository->set([]);

        $this->assertFalse(Config::has('debug'));
    }

    public function testSetAndGetNestedValues(): void
    {
        $this->repository->set([
            'database' => [
                'mysql' => [
                    'host' => '127.0.0.1',
                    'port' => 3306,
                ],
            ],
        ]);

        $this->assertSame('127.0.0.1', Config::get('database.mysql.host'));
        $this->assertSame(3306, Config::get('database.mysql.port'));
    }

    public function testSetValueSetsNewNestedKey(): void
    {
        $this->repository->set([]);

        $this->repository->setValue('cache.driver', 'redis');

        $this->assertSame('redis', Config::get('cache.driver'));
    }

    public function testSetValueOverwritesExistingKey(): void
    {
        $this->repository->set(['cache' => ['driver' => 'file']]);

        $this->repository->setValue('cache.driver', 'redis');

        $this->assertSame('redis', Config::get('cache.driver'));
    }

    public function testAllReturnsAllConfigItems(): void
    {
        $data = ['app' => ['name' => 'Test'], 'debug' => true];
        $this->repository->set($data);

        $this->assertSame($data, $this->repository->all());
    }

    public function testConfigFacadeDelegatesToRepository(): void
    {
        $this->repository->set([
            'mail' => ['host' => 'smtp.example.com'],
        ]);

        $this->assertSame('smtp.example.com', Config::get('mail.host'));
    }
}
