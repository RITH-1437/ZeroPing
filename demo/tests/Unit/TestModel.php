<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\Database\Model;

class TestModel extends Model
{
    protected string $table = 'test_models';
    protected bool $hasSoftDeletes = false;
    protected array $fillable = ['id', 'name', 'email', 'slug'];
}
