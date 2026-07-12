<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\Database\Model;
use App\Core\Database\QueryBuilder;

class ModelTest extends \Tests\TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $ref = new \ReflectionProperty(\App\Core\Database\Database::class, 'connection');
        $ref->setValue(null, $this->createStub(\PDO::class));
    }

    protected function tearDown(): void
    {
        $ref = new \ReflectionProperty(\App\Core\Database\Database::class, 'connection');
        $ref->setValue(null, null);
        parent::tearDown();
    }

    public function testGetTableReturnsTableProperty(): void
    {
        $model = new TestModel();
        $this->assertSame('test_models', $model->getTable());
    }

    public function testGetKeyReturnsId(): void
    {
        $model = new TestModel(['id' => 99, 'name' => 'Test']);
        $this->assertSame(99, $model->getKey());
    }

    public function testGetKeyReturnsNullWhenNoId(): void
    {
        $model = new TestModel(['name' => 'Test']);
        $this->assertNull($model->getKey());
    }

    public function testOffsetExists(): void
    {
        $model = new TestModel(['name' => 'John']);
        $this->assertTrue(isset($model['name']));
        $this->assertFalse(isset($model['missing']));
    }

    public function testOffsetGet(): void
    {
        $model = new TestModel(['name' => 'John']);
        $this->assertSame('John', $model['name']);
    }

    public function testOffsetSet(): void
    {
        $model = new TestModel();
        $model['name'] = 'Jane';
        $this->assertSame('Jane', $model['name']);
    }

    public function testOffsetUnset(): void
    {
        $model = new TestModel(['name' => 'John']);
        unset($model['name']);
        $this->assertFalse(isset($model['name']));
    }

    public function testArrayAccessInterface(): void
    {
        $model = new TestModel();
        $this->assertInstanceOf(\ArrayAccess::class, $model);
    }

    public function testConstructorFillsAttributes(): void
    {
        $model = new TestModel(['id' => 1, 'name' => 'Test', 'email' => 'test@example.com']);
        $this->assertSame(1, $model->id);
        $this->assertSame('Test', $model->name);
        $this->assertSame('test@example.com', $model->email);
    }

    public function testQueryReturnsQueryBuilder(): void
    {
        $model = new TestModel();
        $ref = new \ReflectionProperty(Model::class, 'db');
        $ref->setValue($model, $this->createStub(\PDO::class));

        $qb = $model->query();

        $this->assertInstanceOf(QueryBuilder::class, $qb);
    }

    public function testQuerySetsModelClass(): void
    {
        $model = new TestModel();
        $ref = new \ReflectionProperty(Model::class, 'db');
        $ref->setValue($model, $this->createStub(\PDO::class));

        $qb = $model->query();
        $refQb = new \ReflectionProperty(QueryBuilder::class, 'modelClass');

        $this->assertSame(TestModel::class, $refQb->getValue($qb));
    }

    public function testReplicateCreatesNewInstanceWithoutId(): void
    {
        $model = new TestModel(['id' => 1, 'name' => 'Original']);
        $clone = $model->replicate();

        $this->assertNull($clone->id);
        $this->assertSame('Original', $clone->name);
    }

    public function testReplicateExcludesGivenKeys(): void
    {
        $model = new TestModel(['id' => 1, 'name' => 'Original', 'slug' => 'original']);
        $clone = $model->replicate(['slug']);

        $this->assertNull($clone->slug);
        $this->assertSame('Original', $clone->name);
    }

    public function testStaticQueryReturnsQueryBuilder(): void
    {
        $qb = TestModel::query();
        $this->assertInstanceOf(QueryBuilder::class, $qb);
    }
}
