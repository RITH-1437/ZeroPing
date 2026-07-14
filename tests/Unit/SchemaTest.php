<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\Database\Blueprint;
use App\Core\Database\Connection;
use App\Core\Database\Database;
use App\Core\Database\Drivers\SQLiteDriver;
use App\Core\Database\Grammar\MySqlGrammar;
use App\Core\Database\Grammar\PostgreSqlGrammar;
use App\Core\Database\Grammar\SQLiteGrammar;
use App\Core\Database\Schema;
use PDO;

class SchemaTest extends \Tests\TestCase
{
    protected function tearDown(): void
    {
        Database::manager()->purge();
        parent::tearDown();
    }

    private function sqliteConnection(): Connection
    {
        if (!extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('pdo_sqlite extension is not available.');
        }

        // Each call gets a unique connection name so the in-memory SQLite
        // database is isolated between tests (SQLite :memory: is bound to the
        // PDO handle, so reusing the same name would leak tables across tests).
        $name = 'test_' . uniqid('', true);
        $pdo = new PDO('sqlite::memory:');
        $connection = new Connection(new SQLiteDriver(), $pdo, $name);
        Database::setConnection($name, $connection);

        return $connection;
    }

    public function testMySqlCreateTableCompilation(): void
    {
        $blueprint = new Blueprint('users', new MySqlGrammar());
        $blueprint->id();
        $blueprint->string('email', 255);
        $blueprint->timestamps();

        $sql = $blueprint->toSql()[0];

        $this->assertStringContainsString('CREATE TABLE IF NOT EXISTS `users`', $sql);
        $this->assertStringContainsString('`id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY', $sql);
        $this->assertStringContainsString('`email` VARCHAR(255) NOT NULL', $sql);
        $this->assertStringContainsString('`created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP', $sql);
        // The auto-increment id carries the PRIMARY KEY inline; no duplicate
        // table-level constraint is emitted.
        $this->assertStringNotContainsString('PRIMARY KEY (`id`)', $sql);
    }

    public function testSqliteCreateTableCompilation(): void
    {
        $blueprint = new Blueprint('users', new SQLiteGrammar());
        $blueprint->id();
        $blueprint->string('email', 255);

        $sql = $blueprint->toSql()[0];

        $this->assertStringContainsString('CREATE TABLE IF NOT EXISTS "users"', $sql);
        $this->assertStringContainsString('"id" INTEGER PRIMARY KEY AUTOINCREMENT', $sql);
        $this->assertStringContainsString('"email" TEXT NOT NULL', $sql);
    }

    public function testPostgreSqlCreateTableCompilation(): void
    {
        $blueprint = new Blueprint('users', new PostgreSqlGrammar());
        $blueprint->id();
        $blueprint->string('email', 255);
        $blueprint->timestamps();

        $sql = $blueprint->toSql()[0];

        $this->assertStringContainsString('CREATE TABLE IF NOT EXISTS "users"', $sql);
        $this->assertStringContainsString('"id" BIGSERIAL PRIMARY KEY', $sql);
        $this->assertStringContainsString('"email" VARCHAR(255) NOT NULL', $sql);
        $this->assertStringContainsString('"created_at" TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP', $sql);
    }

    public function testAlterTableAddColumnCompilation(): void
    {
        $blueprint = new Blueprint('users', new MySqlGrammar());
        $blueprint->string('phone', 100)->nullable();

        $sql = $blueprint->toAlterSql()[0];

        $this->assertStringContainsString('ALTER TABLE `users` ADD COLUMN `phone` VARCHAR(100) NULL', $sql);
    }

    public function testDropColumnCompilation(): void
    {
        $blueprint = new Blueprint('users', new MySqlGrammar());
        $blueprint->dropColumn('phone', 'avatar');

        $sql = $blueprint->toAlterSql()[0];

        $this->assertStringContainsString('ALTER TABLE `users` DROP COLUMN `phone`, `avatar`', $sql);
    }

    public function testDropTableIsIdentifierSafe(): void
    {
        $grammar = new MySqlGrammar();
        $sql = $grammar->compileDrop("users; DROP TABLE users2`");

        // The whole argument is wrapped as a single quoted identifier, so it
        // can never break out into a second statement.
        $this->assertSame('DROP TABLE IF EXISTS `users; DROP TABLE users2```', $sql);
    }

    public function testHasTableUsesConnectionGrammar(): void
    {
        $connection = $this->sqliteConnection();

        $this->assertFalse(Schema::connection($connection)->hasTable('widgets'));
    }

    public function testCreateAndDropRoundTripOnSqlite(): void
    {
        $connection = $this->sqliteConnection();

        Schema::connection($connection)->create('widgets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
        });

        $this->assertTrue($connection->hasTable('widgets'));

        Schema::connection($connection)->drop('widgets');

        $this->assertFalse($connection->hasTable('widgets'));
    }

    public function testEnumCompilation(): void
    {
        $blueprint = new Blueprint('users', new MySqlGrammar());
        $blueprint->enum('role', ['admin', 'owner', 'customer'])->default('customer');

        $sql = $blueprint->toSql()[0];
        $normalized = str_replace(' ', '', $sql);

        $this->assertStringContainsString(
            "`role`ENUMCHECK(`role`IN('admin','owner','customer'))NOTNULLDEFAULT'customer'",
            $normalized
        );
    }
}
