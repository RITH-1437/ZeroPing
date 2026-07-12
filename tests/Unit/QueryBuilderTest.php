<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\Database\QueryBuilder;

class QueryBuilderTest extends \Tests\TestCase
{
    private function createStubPdo(): \PDO
    {
        $stub = $this->createStub(\PDO::class);
        return $stub;
    }

    private function createQueryBuilderWithoutDb(): QueryBuilder
    {
        return new QueryBuilder($this->createStubPdo(), 'users');
    }

    public function testSelectGeneratesCorrectSql(): void
    {
        $qb = $this->createQueryBuilderWithoutDb();

        $sql = $qb->toSql();

        $this->assertStringContainsString('SELECT * FROM users', $sql);
        $this->assertStringContainsString('deleted_at IS NULL', $sql);
    }

    public function testSelectSpecificColumns(): void
    {
        $qb = $this->createQueryBuilderWithoutDb();

        $sql = $qb->select('name', 'email')->toSql();

        $this->assertStringContainsString('SELECT name, email FROM users', $sql);
    }

    public function testSelectWithArray(): void
    {
        $qb = $this->createQueryBuilderWithoutDb();

        $sql = $qb->select(['name', 'email'])->toSql();

        $this->assertStringContainsString('SELECT name, email FROM users', $sql);
    }

    public function testWhereAddsCondition(): void
    {
        $qb = $this->createQueryBuilderWithoutDb();

        $sql = $qb->where('name', 'John')->toSql();

        $this->assertStringContainsString('WHERE name = ?', $sql);
    }

    public function testWhereWithOperator(): void
    {
        $qb = $this->createQueryBuilderWithoutDb();

        $sql = $qb->where('age', 18, '>=')->toSql();

        $this->assertStringContainsString('WHERE age >= ?', $sql);
    }

    public function testMultipleWheres(): void
    {
        $qb = $this->createQueryBuilderWithoutDb();

        $sql = $qb->where('name', 'John')->where('age', 30)->toSql();

        $this->assertStringContainsString('name = ?', $sql);
        $this->assertStringContainsString('AND age = ?', $sql);
    }

    public function testOrWhere(): void
    {
        $qb = $this->createQueryBuilderWithoutDb();

        $sql = $qb->where('name', 'John')->orWhere('name', 'Jane')->toSql();

        $this->assertStringContainsString('OR name = ?', $sql);
    }

    public function testOrWhereOnEmptyWhereFallsBackToWhere(): void
    {
        $qb = $this->createQueryBuilderWithoutDb();

        $sql = $qb->orWhere('name', 'Jane')->toSql();

        $this->assertStringContainsString('WHERE name = ?', $sql);
    }

    public function testWhereIn(): void
    {
        $qb = $this->createQueryBuilderWithoutDb();

        $sql = $qb->whereIn('id', [1, 2, 3])->toSql();

        $this->assertStringContainsString('id IN (?,?,?)', $sql);
    }

    public function testWhereNull(): void
    {
        $qb = $this->createQueryBuilderWithoutDb();

        $sql = $qb->whereNull('deleted_at')->toSql();

        $this->assertStringContainsString('deleted_at IS NULL', $sql);
    }

    public function testWhereNotNull(): void
    {
        $qb = $this->createQueryBuilderWithoutDb();

        $sql = $qb->whereNotNull('email')->toSql();

        $this->assertStringContainsString('email IS NOT NULL', $sql);
    }

    public function testOrderBy(): void
    {
        $qb = $this->createQueryBuilderWithoutDb();

        $sql = $qb->orderBy('name', 'DESC')->toSql();

        $this->assertStringContainsString('ORDER BY name DESC', $sql);
    }

    public function testOrderByDefaultsToAsc(): void
    {
        $qb = $this->createQueryBuilderWithoutDb();

        $sql = $qb->orderBy('name')->toSql();

        $this->assertStringContainsString('ORDER BY name ASC', $sql);
    }

    public function testOrderByInvalidDirectionDefaultsToAsc(): void
    {
        $qb = $this->createQueryBuilderWithoutDb();

        $sql = $qb->orderBy('name', 'SIDEWAYS')->toSql();

        $this->assertStringContainsString('ORDER BY name ASC', $sql);
    }

    public function testMultipleOrderBy(): void
    {
        $qb = $this->createQueryBuilderWithoutDb();

        $sql = $qb->orderBy('name', 'ASC')->orderBy('id', 'DESC')->toSql();

        $this->assertStringContainsString('ORDER BY name ASC, id DESC', $sql);
    }

    public function testLatestAddsDescOrderBy(): void
    {
        $qb = $this->createQueryBuilderWithoutDb();

        $sql = $qb->latest()->toSql();

        $this->assertStringContainsString('ORDER BY created_at DESC', $sql);
    }

    public function testOldestAddsAscOrderBy(): void
    {
        $qb = $this->createQueryBuilderWithoutDb();

        $sql = $qb->oldest()->toSql();

        $this->assertStringContainsString('ORDER BY created_at ASC', $sql);
    }

    public function testGroupBy(): void
    {
        $qb = $this->createQueryBuilderWithoutDb();

        $sql = $qb->groupBy('age')->toSql();

        $this->assertStringContainsString('GROUP BY age', $sql);
    }

    public function testGroupByArray(): void
    {
        $qb = $this->createQueryBuilderWithoutDb();

        $sql = $qb->groupBy(['age', 'status'])->toSql();

        $this->assertStringContainsString('GROUP BY age, status', $sql);
    }

    public function testHaving(): void
    {
        $qb = $this->createQueryBuilderWithoutDb();

        $sql = $qb->groupBy('age')->having('age', '>', 18)->toSql();

        $this->assertStringContainsString('HAVING age > ?', $sql);
    }

    public function testLimit(): void
    {
        $qb = $this->createQueryBuilderWithoutDb();

        $sql = $qb->limit(10)->toSql();

        $this->assertStringContainsString('LIMIT 10', $sql);
    }

    public function testOffset(): void
    {
        $qb = $this->createQueryBuilderWithoutDb();

        $sql = $qb->limit(10)->offset(20)->toSql();

        $this->assertStringContainsString('LIMIT 10', $sql);
        $this->assertStringContainsString('OFFSET 20', $sql);
    }

    public function testTakeAliasesToLimit(): void
    {
        $qb = $this->createQueryBuilderWithoutDb();

        $sql = $qb->take(5)->toSql();

        $this->assertStringContainsString('LIMIT 5', $sql);
    }

    public function testSkipAliasesToOffset(): void
    {
        $qb = $this->createQueryBuilderWithoutDb();

        $sql = $qb->take(10)->skip(5)->toSql();

        $this->assertStringContainsString('LIMIT 10', $sql);
        $this->assertStringContainsString('OFFSET 5', $sql);
    }

    public function testResetClearsAllConstraints(): void
    {
        $qb = $this->createQueryBuilderWithoutDb();

        $sql1 = $qb->where('name', 'John')->limit(5)->orderBy('id')->toSql();

        $qb->reset();

        $sql2 = $qb->toSql();

        $this->assertStringContainsString('name = ?', $sql1);
        $this->assertStringNotContainsString('name = ?', $sql2);
        $this->assertStringNotContainsString('LIMIT', $sql2);
        $this->assertStringNotContainsString('ORDER BY', $sql2);
    }

    public function testWithTrashedRemovesDeletedAtFilter(): void
    {
        $qb = $this->createQueryBuilderWithoutDb();

        $sqlWithoutSoftDelete = $qb->toSql();
        $this->assertStringContainsString('deleted_at IS NULL', $sqlWithoutSoftDelete);

        $qb->reset();
        $sqlWithTrashed = $qb->withTrashed()->toSql();
        $this->assertStringNotContainsString('deleted_at IS NULL', $sqlWithTrashed);
    }

    public function testOnlyTrashedFiltersDeletedRows(): void
    {
        $qb = $this->createQueryBuilderWithoutDb();

        $sql = $qb->onlyTrashed()->toSql();

        $this->assertStringContainsString('deleted_at IS NOT NULL', $sql);
    }

    public function testComplexQuery(): void
    {
        $qb = $this->createQueryBuilderWithoutDb();

        $sql = $qb
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

    public function testSelectWithModelClassSetsModelClass(): void
    {
        $qb = $this->createQueryBuilderWithoutDb();

        $result = $qb->setModelClass('App\\Models\\User');

        $this->assertSame($qb, $result);
    }
}
