<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\Database\QueryBuilder;

/**
 * @covers \App\Core\Database\QueryBuilder
 */
class QueryBuilderTest extends \Tests\TestCase
{
    private function createQueryBuilder(): QueryBuilder
    {
        return new QueryBuilder($this->createStub(\PDO::class), 'users');
    }

    // ─── SELECT ──────────────────────────────────────────────────────

    public function testSelectAllGeneratesCorrectSql(): void
    {
        $sql = $this->createQueryBuilder()->toSql();

        $this->assertStringContainsString('SELECT * FROM users', $sql);
        $this->assertStringNotContainsString('deleted_at IS NULL', $sql);
    }

    public function testSelectSpecificColumnsAsArguments(): void
    {
        $sql = $this->createQueryBuilder()->select('name', 'email')->toSql();

        $this->assertStringContainsString('SELECT name, email FROM users', $sql);
    }

    public function testSelectSpecificColumnsAsArray(): void
    {
        $sql = $this->createQueryBuilder()->select(['name', 'email'])->toSql();

        $this->assertStringContainsString('SELECT name, email FROM users', $sql);
    }

    // ─── WHERE ───────────────────────────────────────────────────────

    public function testWhereAddsCondition(): void
    {
        $sql = $this->createQueryBuilder()->where('name', 'John')->toSql();

        $this->assertStringContainsString('WHERE name = ?', $sql);
    }

    public function testWhereWithCustomOperator(): void
    {
        $sql = $this->createQueryBuilder()->where('age', 18, '>=')->toSql();

        $this->assertStringContainsString('WHERE age >= ?', $sql);
    }

    public function testMultipleWheresJoinedWithAnd(): void
    {
        $sql = $this->createQueryBuilder()->where('name', 'John')->where('age', 30)->toSql();

        $this->assertStringContainsString('name = ?', $sql);
        $this->assertStringContainsString('AND age = ?', $sql);
    }

    public function testOrWhereAddsOrCondition(): void
    {
        $sql = $this->createQueryBuilder()->where('name', 'John')->orWhere('name', 'Jane')->toSql();

        $this->assertStringContainsString('OR name = ?', $sql);
    }

    public function testOrWhereOnEmptyWhereFallsBackToWhere(): void
    {
        $sql = $this->createQueryBuilder()->orWhere('name', 'Jane')->toSql();

        $this->assertStringContainsString('WHERE name = ?', $sql);
    }

    public function testWhereInWithValues(): void
    {
        $sql = $this->createQueryBuilder()->whereIn('id', [1, 2, 3])->toSql();

        $this->assertStringContainsString('id IN (?,?,?)', $sql);
    }

    public function testWhereNullAddsIsNullCondition(): void
    {
        $sql = $this->createQueryBuilder()->whereNull('deleted_at')->toSql();

        $this->assertStringContainsString('deleted_at IS NULL', $sql);
    }

    public function testWhereNotNullAddsIsNotNullCondition(): void
    {
        $sql = $this->createQueryBuilder()->whereNotNull('email')->toSql();

        $this->assertStringContainsString('email IS NOT NULL', $sql);
    }

    // ─── ORDER BY ────────────────────────────────────────────────────

    public function testOrderByDescending(): void
    {
        $sql = $this->createQueryBuilder()->orderBy('name', 'DESC')->toSql();

        $this->assertStringContainsString('ORDER BY name DESC', $sql);
    }

    public function testOrderByDefaultsToAsc(): void
    {
        $sql = $this->createQueryBuilder()->orderBy('name')->toSql();

        $this->assertStringContainsString('ORDER BY name ASC', $sql);
    }

    public function testOrderByInvalidDirectionDefaultsToAsc(): void
    {
        $sql = $this->createQueryBuilder()->orderBy('name', 'SIDEWAYS')->toSql();

        $this->assertStringContainsString('ORDER BY name ASC', $sql);
    }

    public function testMultipleOrderByClauses(): void
    {
        $sql = $this->createQueryBuilder()->orderBy('name', 'ASC')->orderBy('id', 'DESC')->toSql();

        $this->assertStringContainsString('ORDER BY name ASC, id DESC', $sql);
    }

    public function testLatestOrdersByCreatedAtDesc(): void
    {
        $sql = $this->createQueryBuilder()->latest()->toSql();

        $this->assertStringContainsString('ORDER BY created_at DESC', $sql);
    }

    public function testOldestOrdersByCreatedAtAsc(): void
    {
        $sql = $this->createQueryBuilder()->oldest()->toSql();

        $this->assertStringContainsString('ORDER BY created_at ASC', $sql);
    }

    // ─── GROUP BY & HAVING ───────────────────────────────────────────

    public function testGroupByColumn(): void
    {
        $sql = $this->createQueryBuilder()->groupBy('age')->toSql();

        $this->assertStringContainsString('GROUP BY age', $sql);
    }

    public function testGroupByMultipleColumns(): void
    {
        $sql = $this->createQueryBuilder()->groupBy(['age', 'status'])->toSql();

        $this->assertStringContainsString('GROUP BY age, status', $sql);
    }

    public function testHavingClause(): void
    {
        $sql = $this->createQueryBuilder()->groupBy('age')->having('age', '>', 18)->toSql();

        $this->assertStringContainsString('HAVING age > ?', $sql);
    }

    // ─── LIMIT & OFFSET ─────────────────────────────────────────────

    public function testLimitClause(): void
    {
        $sql = $this->createQueryBuilder()->limit(10)->toSql();

        $this->assertStringContainsString('LIMIT 10', $sql);
    }

    public function testOffsetClause(): void
    {
        $sql = $this->createQueryBuilder()->limit(10)->offset(20)->toSql();

        $this->assertStringContainsString('LIMIT 10', $sql);
        $this->assertStringContainsString('OFFSET 20', $sql);
    }

    public function testTakeIsAliasForLimit(): void
    {
        $sql = $this->createQueryBuilder()->take(5)->toSql();

        $this->assertStringContainsString('LIMIT 5', $sql);
    }

    public function testSkipIsAliasForOffset(): void
    {
        $sql = $this->createQueryBuilder()->take(10)->skip(5)->toSql();

        $this->assertStringContainsString('LIMIT 10', $sql);
        $this->assertStringContainsString('OFFSET 5', $sql);
    }

    // ─── Reset ───────────────────────────────────────────────────────

    public function testResetClearsAllConstraints(): void
    {
        $qb = $this->createQueryBuilder();

        $sql1 = $qb->where('name', 'John')->limit(5)->orderBy('id')->toSql();

        $qb->reset();

        $sql2 = $qb->toSql();

        $this->assertStringContainsString('name = ?', $sql1);
        $this->assertStringNotContainsString('name = ?', $sql2);
        $this->assertStringNotContainsString('LIMIT', $sql2);
        $this->assertStringNotContainsString('ORDER BY', $sql2);
    }

    // ─── Soft Deletes ────────────────────────────────────────────────

    public function testSoftDeletesAreOptIn(): void
    {
        $qb = $this->createQueryBuilder();

        $this->assertStringNotContainsString('deleted_at IS NULL', $qb->toSql());

        $qb->reset();
        $this->assertStringContainsString('deleted_at IS NULL', $qb->softDeletes()->toSql());

        $qb->reset();
        $this->assertStringNotContainsString('deleted_at IS NULL', $qb->withTrashed()->toSql());

        $qb->reset();
        $this->assertStringContainsString('deleted_at IS NOT NULL', $qb->onlyTrashed()->toSql());
    }

    public function testOnlyTrashedFiltersDeletedRows(): void
    {
        $sql = $this->createQueryBuilder()->onlyTrashed()->toSql();

        $this->assertStringContainsString('deleted_at IS NOT NULL', $sql);
    }

    public function testSoftDeleteSqlDoesNotAccumulateAcrossCompilation(): void
    {
        $qb = $this->createQueryBuilder()->softDeletes();

        $this->assertSame($qb->toSql(), $qb->toSql());
    }

    // ─── Complex Queries ─────────────────────────────────────────────

    public function testComplexQueryWithMultipleClauses(): void
    {
        $sql = $this->createQueryBuilder()
            ->select('name', 'age')
            ->where('age', 18, '>=')
            ->where('name', '%John%', 'LIKE')
            ->orderBy('name')
            ->limit(10)
            ->offset(5)
            ->toSql();

        $this->assertStringContainsString('SELECT name, age FROM users', $sql);
        $this->assertStringContainsString('age >= ?', $sql);
        $this->assertStringContainsString('AND name LIKE ?', $sql);
        $this->assertStringContainsString('ORDER BY name ASC', $sql);
        $this->assertStringContainsString('LIMIT 10', $sql);
        $this->assertStringContainsString('OFFSET 5', $sql);
    }

    // ─── Model Class ─────────────────────────────────────────────────

    public function testSetModelClassReturnsSelf(): void
    {
        $qb = $this->createQueryBuilder();

        $result = $qb->setModelClass('App\\Models\\User');

        $this->assertSame($qb, $result);
    }

    // ─── Input Validation ────────────────────────────────────────────

    public function testRejectsUnsafeColumnIdentifier(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->createQueryBuilder()->where('name; DROP TABLE users', 'John');
    }

    public function testRejectsUnsafeOperator(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->createQueryBuilder()->where('name', 'John', '= 1; DROP TABLE users');
    }

    public function testEmptyWhereInCompilesToNoResults(): void
    {
        $sql = $this->createQueryBuilder()->whereIn('id', [])->toSql();

        $this->assertStringContainsString('WHERE 0 = 1', $sql);
    }
}
