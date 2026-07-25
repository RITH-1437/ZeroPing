<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\Database\ConnectionFactory;
use App\Core\Database\DatabaseManager;
use App\Core\Database\Drivers\DriverInterface;
use App\Core\Database\Drivers\MariaDbDriver;
use App\Core\Database\Drivers\MySqlDriver;
use App\Core\Database\Drivers\PostgreSqlDriver;
use App\Core\Database\Drivers\SQLiteDriver;
use App\Core\Database\Grammar\Grammar;
use PHPUnit\Framework\TestCase;

class ConnectionFactoryTest extends TestCase
{
    private function factory(): ConnectionFactory
    {
        return new ConnectionFactory();
    }

    public function testAvailableDriversIncludeAllFour(): void
    {
        $drivers = $this->factory()->availableDrivers();

        foreach (['sqlite', 'mysql', 'mariadb', 'pgsql'] as $expected) {
            $this->assertContains($expected, $drivers, "Driver [{$expected}] should be registered.");
        }
    }

    public function testDriverMappingResolvesConcreteClasses(): void
    {
        $factory = $this->factory();

        $this->assertInstanceOf(SQLiteDriver::class, $factory->driver('sqlite'));
        $this->assertInstanceOf(MySqlDriver::class, $factory->driver('mysql'));
        $this->assertInstanceOf(MariaDbDriver::class, $factory->driver('mariadb'));
        $this->assertInstanceOf(PostgreSqlDriver::class, $factory->driver('pgsql'));
    }

    public function testMariaDbUsesMySqlDriver(): void
    {
        $this->assertInstanceOf(
            MySqlDriver::class,
            $this->factory()->driver('mariadb')
        );
    }

    public function testUnknownDriverThrows(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->factory()->driver('sqlserver');
    }

    public function testDriverInterfaceContract(): void
    {
        $expected = [
            'sqlite'  => ['name' => 'sqlite',  'pdo' => 'pdo_sqlite', 'grammar' => \App\Core\Database\Grammar\SQLiteGrammar::class],
            'mysql'   => ['name' => 'mysql',   'pdo' => 'pdo_mysql',  'grammar' => \App\Core\Database\Grammar\MySqlGrammar::class],
            'mariadb' => ['name' => 'mariadb', 'pdo' => 'pdo_mysql',  'grammar' => \App\Core\Database\Grammar\MySqlGrammar::class],
            'pgsql'   => ['name' => 'pgsql',   'pdo' => 'pdo_pgsql',  'grammar' => \App\Core\Database\Grammar\PostgreSqlGrammar::class],
        ];

        foreach ($expected as $key => $meta) {
            $driver = $this->factory()->driver($key);

            $this->assertInstanceOf(DriverInterface::class, $driver);
            $this->assertSame($meta['name'], $driver->getName());
            $this->assertSame($meta['pdo'], $driver->getPdoExtension());
            $this->assertInstanceOf(Grammar::class, $driver->grammar());
            $this->assertInstanceOf($meta['grammar'], $driver->grammar());
        }
    }

    public function testQuoteIdentifierIsEngineSpecific(): void
    {
        $this->assertSame('`users`', $this->factory()->driver('mysql')->quoteIdentifier('users'));
        $this->assertSame('`users`', $this->factory()->driver('mariadb')->quoteIdentifier('users'));
        $this->assertSame('"users"', $this->factory()->driver('sqlite')->quoteIdentifier('users'));
        $this->assertSame('"users"', $this->factory()->driver('pgsql')->quoteIdentifier('users'));
    }

    public function testSupportsFeatureFlags(): void
    {
        $mysql = $this->factory()->driver('mysql');
        $sqlite = $this->factory()->driver('sqlite');

        $this->assertTrue($mysql->supports('transactions'));
        $this->assertTrue($sqlite->supports('transactions'));
        $this->assertTrue($sqlite->supports('foreign_keys'));
    }

    public function testDatabaseManagerExposesSameDrivers(): void
    {
        $manager = new DatabaseManager();

        foreach (['sqlite', 'mysql', 'mariadb', 'pgsql'] as $expected) {
            $this->assertContains(
                $expected,
                $manager->availableDrivers(),
                "DatabaseManager should support [{$expected}]."
            );
        }
    }

    public function testSqliteDriverCanConnectInMemory(): void
    {
        if (!extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('pdo_sqlite extension is not available.');
        }

        $pdo = $this->factory()->driver('sqlite')->connect(['database' => ':memory:']);

        $this->assertInstanceOf(\PDO::class, $pdo);
    }
}
