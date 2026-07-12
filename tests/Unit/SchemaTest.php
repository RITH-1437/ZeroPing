<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\Database\Schema;
use App\Core\Database\Database;

class SchemaTest extends \Tests\TestCase
{
    private function createStubPdo(): \PDO
    {
        return $this->createStub(\PDO::class);
    }

    protected function tearDown(): void
    {
        $ref = new \ReflectionProperty(\App\Core\Database\Database::class, 'connection');
        $ref->setValue(null, null);
        parent::tearDown();
    }

    public function testConstructorSetsDb(): void
    {
        $pdo = $this->createStubPdo();
        $schema = new Schema($pdo);

        $this->assertInstanceOf(Schema::class, $schema);
    }

    public function testHasTableUsesPreparedStatement(): void
    {
        $stmt = $this->createMock(\PDOStatement::class);
        $stmt->expects($this->once())
            ->method('execute')
            ->with(['users']);
        $stmt->method('rowCount')->willReturn(1);

        $pdo = $this->createMock(\PDO::class);
        $pdo->expects($this->once())
            ->method('prepare')
            ->with($this->stringContains('SHOW TABLES LIKE ?'))
            ->willReturn($stmt);

        $schema = new Schema($pdo);

        $this->assertTrue($schema->hasTable('users'));
    }

    public function testHasTableReturnsFalseWhenNoRows(): void
    {
        $stmt = $this->createStub(\PDOStatement::class);
        $stmt->method('execute')->willReturn(true);
        $stmt->method('rowCount')->willReturn(0);

        $pdo = $this->createStub(\PDO::class);
        $pdo->method('prepare')->willReturn($stmt);

        $schema = new Schema($pdo);

        $this->assertFalse($schema->hasTable('missing'));
    }

    public function testDropUsesBacktickQuotedTableName(): void
    {
        $stmt = $this->createMock(\PDOStatement::class);
        $stmt->expects($this->once())
            ->method('execute')
            ->willReturn(true);

        $pdo = $this->createMock(\PDO::class);
        $pdo->expects($this->once())
            ->method('prepare')
            ->with('DROP TABLE IF EXISTS `users`')
            ->willReturn($stmt);

        $ref = new \ReflectionProperty(\App\Core\Database\Database::class, 'connection');
        $ref->setValue(null, $pdo);

        $schema = new Schema($pdo);

        $schema->drop('users');
    }

    public function testDropDoesNotAllowSqlInjection(): void
    {
        $stmt = $this->createMock(\PDOStatement::class);
        $stmt->expects($this->once())
            ->method('execute')
            ->willReturn(true);

        $pdo = $this->createMock(\PDO::class);
        $pdo->expects($this->once())
            ->method('prepare')
            ->with('DROP TABLE IF EXISTS `users; DROP TABLE users2`')
            ->willReturn($stmt);

        $ref = new \ReflectionProperty(\App\Core\Database\Database::class, 'connection');
        $ref->setValue(null, $pdo);

        $schema = new Schema($pdo);

        $schema->drop('users; DROP TABLE users2');
    }
}
