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

    protected bool $exists = false;

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
    | Query Builder
    |--------------------------------------------------------------------------
    */

    public static function query(): Builder
    {
        return new Builder(new static());
    }

    public static function all(): Collection
    {
        return static::query()->all();
    }

    public static function find(mixed $id): ?static
    {
        return static::query()->find($id);
    }

    public static function where(
        string $column,
        string $operator,
        mixed $value = null
    ): Builder {

        return static::query()->where(
            $column,
            $operator,
            $value
        );
    }

    public static function create(array $attributes): static
    {
        $model = new static($attributes);

        $model->save();

        return $model;
    }

    /*
    |--------------------------------------------------------------------------
    | Persistence
    |--------------------------------------------------------------------------
    */

    public function save(): bool
    {
        return (new Persister())
            ->save($this);
    }

    public function update(array $attributes): bool
    {
        $this->fill($attributes);

        return $this->save();
    }

    public function delete(): bool
    {
        return (new Persister())
            ->delete($this);
    }

    /*
    |--------------------------------------------------------------------------
    | Model State
    |--------------------------------------------------------------------------
    */

    public function exists(): bool
    {
        return $this->exists;
    }

    public function setExists(bool $exists): static
    {
        $this->exists = $exists;

        return $this;
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