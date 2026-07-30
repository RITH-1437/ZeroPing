<?php

declare(strict_types=1);

namespace App\Core\ORM;

use App\Core\ORM\Concerns\HasAttributes;

/**
 * Legacy Model base class.
 *
 * @deprecated since 1.x — Use {@see \App\Core\Database\Model} instead.
 *
 * This class is retained for backward compatibility only. It uses a
 * Persister/Builder/Hydrator pattern that has been superseded by the
 * full-featured Database\Model which provides:
 * - Direct PDO + QueryBuilder integration
 * - Soft deletes, events, relationships, ArrayAccess
 * - Mass-assignment protection (GuardsAttributes)
 * - Timestamps
 *
 * All new models should extend \App\Core\Database\Model.
 * This class will be removed in a future major version.
 *
 * @see \App\Core\Database\Model The replacement base model class.
 */
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
        trigger_error(
            sprintf(
                '%s extends deprecated %s. Migrate to \App\Core\Database\Model.',
                static::class,
                self::class
            ),
            E_USER_DEPRECATED
        );

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
        return static::query()->where($column, $operator, $value);
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
        return (new Persister())->save($this);
    }

    public function update(array $attributes): bool
    {
        $this->fill($attributes);

        return $this->save();
    }

    public function delete(): bool
    {
        return (new Persister())->delete($this);
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
