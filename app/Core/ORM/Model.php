<?php

namespace App\Core\ORM;

use App\Core\ORM\Concerns\HasAttributes;

abstract class Model
{
    use HasAttributes;

    /*
    |--------------------------------------------------------------------------
    | Model Metadata
    |--------------------------------------------------------------------------
    */

    protected string $table = '';

    protected string $primaryKey = 'id';

    public bool $timestamps = true;

    protected array $fillable = [];

    protected array $guarded = ['*'];

    protected array $casts = [];

    /*
    |--------------------------------------------------------------------------
    | Original Attributes
    |--------------------------------------------------------------------------
    */

    protected array $original = [];

    /*
    |--------------------------------------------------------------------------
    | Constructor
    |--------------------------------------------------------------------------
    */

    public function __construct(array $attributes = [])
    {
        $this->fill($attributes);

        $this->syncOriginal();
    }

    /*
    |--------------------------------------------------------------------------
    | Builder
    |--------------------------------------------------------------------------
    */

    public static function query(): Builder
    {
        return new Builder(new static());
    }

    /*
    |--------------------------------------------------------------------------
    | Metadata
    |--------------------------------------------------------------------------
    */

    public function getTable(): string
    {
        return $this->table;
    }

    public function getPrimaryKey(): string
    {
        return $this->primaryKey;
    }

    public function getFillable(): array
    {
        return $this->fillable;
    }

    public function getGuarded(): array
    {
        return $this->guarded;
    }

    public function getCasts(): array
    {
        return $this->casts;
    }

    /*
    |--------------------------------------------------------------------------
    | Original State
    |--------------------------------------------------------------------------
    */

    public function syncOriginal(): void
    {
        $this->original = $this->attributes;
    }

    public function getOriginal(): array
    {
        return $this->original;
    }

    public function isDirty(): bool
    {
        return $this->attributes !== $this->original;
    }

    public function isClean(): bool
    {
        return !$this->isDirty();
    }
}