<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Core\Database\Database;
use App\Core\Testing\TestCase;

class DatabaseAssertionsTest extends TestCase
{
    private const TABLE = '_zp_assertions';

    protected function setUp(): void
    {
        parent::setUp();

        $pdo = Database::connect();
        $pdo->exec('CREATE TABLE IF NOT EXISTS ' . self::TABLE . ' (id INTEGER PRIMARY KEY, name TEXT)');
        $pdo->exec('DELETE FROM ' . self::TABLE);
        $pdo->exec('INSERT INTO ' . self::TABLE . " (id, name) VALUES (1, 'Ada')");
    }

    protected function tearDown(): void
    {
        Database::connect()->exec('DROP TABLE IF EXISTS ' . self::TABLE);

        parent::tearDown();
    }

    public function test_assert_database_has_and_missing(): void
    {
        $this->assertDatabaseHas(self::TABLE, ['name' => 'Ada']);
        $this->assertDatabaseMissing(self::TABLE, ['name' => 'None']);
    }
}
