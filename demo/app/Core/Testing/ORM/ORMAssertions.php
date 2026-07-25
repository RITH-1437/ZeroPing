<?php

namespace App\Core\Testing\ORM;

use App\Core\Database\Model;

trait ORMAssertions
{
    public function assertModelExists(Model $model): void
    {
        $this->assertDatabaseHas($model->getTable(), ['id' => $model->id]);
    }

    public function assertSoftDeleted(Model $model): void
    {
        $this->assertDatabaseHas($model->getTable(), ['id' => $model->id])
             ->assertNotNull($model->deleted_at);
    }

    public function assertRelationship(Model $parent, Model $child, string $relation): void
    {
        $this->assertTrue($parent->{$relation}->contains($child));
    }
}
