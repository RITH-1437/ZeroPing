<?php

namespace App\Core\Database;

use App\Core\Database\QueryBuilder;
use App\Core\ORM\Collection;
use App\Core\ORM\Concerns\GuardsAttributes;
use App\Core\ORM\Concerns\HasAttributes;
use App\Core\ORM\Concerns\HasEvents;
use App\Core\ORM\Concerns\HasRelationships;
use App\Core\ORM\Concerns\HasTimestamps;
use App\Core\ORM\Concerns\SoftDeletes;
use PDO;

abstract class Model implements \ArrayAccess
{
    use GuardsAttributes, HasTimestamps, HasAttributes, HasRelationships, SoftDeletes, HasEvents {
        HasRelationships::__get insteadof HasAttributes;
        HasAttributes::__get as getAttributeMagic;
    }

    protected PDO $db;

    protected string $table;

    /** Set to false on models whose tables have no deleted_at column */
    protected bool $hasSoftDeletes = true;

    public function __construct(array $attributes = [])
    {
        $this->db = Database::connect();
        $this->fill($attributes);
    }

    /**
     * Create a new query builder instance.
     */
    public static function query(): QueryBuilder
    {
        $instance = new static;
        $qb = new QueryBuilder($instance->db, $instance->table);

        if (!$instance->hasSoftDeletes) {
            $qb->withTrashed();
        }

        return $qb->setModelClass(static::class);
    }

    /**
     * Get all records.
     */
    public static function all(): Collection
    {
        return (new static)->query()->get();
    }

    /**
     * Find a record by primary key.
     */
    public static function find(int|string $id): ?static
    {
        return (new static)->query()
            ->where('id', (int) $id)
            ->first();
    }

    /**
     * Get the primary key value for this model instance.
     */
    public function getKey(): mixed
    {
        return $this->attributes['id'] ?? null;
    }

    /**
     * Get the table name for this model.
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * Find first record by column.
     */
    public static function findBy(string $column, mixed $value): ?static
    {
        return (new static)->query()
            ->where($column, $value)
            ->first();
    }

    /**
     * Insert a new record.
     */
    public static function create(array $attributes = []): static
    {
        $model = new static($attributes);
        $model->save();
        return $model;
    }

    /**
     * Save the model to the database.
     */
    public function save(): bool
    {
        if ($this->fireModelEvent('saving') === false) {
            return false;
        }

        if ($this->timestamps) {
            $this->updateTimestamps();
        }

        if (isset($this->attributes['id'])) {
            $result = $this->performUpdate();
        } else {
            $result = $this->insert();
        }

        if ($result) {
            $this->fireModelEvent('saved', false);
        }

        return $result;
    }

    /**
     * Update the model with the given attributes and save.
     */
    public function update(array $attributes = []): bool
    {
        $this->fill($attributes);
        return $this->save();
    }

    /**
     * Perform the actual UPDATE query.
     */
    protected function performUpdate(): bool
    {
        if ($this->fireModelEvent('updating') === false) {
            return false;
        }

        $fields = [];
        $values = [];

        foreach ($this->attributes as $column => $value) {
            if ($column !== 'id') {
                $fields[] = "{$column} = ?";
                $values[] = $value;
            }
        }

        $sql = implode(', ', $fields);
        $values[] = $this->attributes['id'];

        $stmt = $this->db->prepare(
            "UPDATE {$this->table}
             SET {$sql}
             WHERE id = ?"
        );

        $result = $stmt->execute($values);

        if ($result) {
            $this->fireModelEvent('updated', false);
        }

        return $result;
    }

    /**
     * Insert a new record.
     */
    protected function insert(): bool
    {
        if ($this->fireModelEvent('creating') === false) {
            return false;
        }

        $columns = implode(',', array_keys($this->attributes));
        $placeholders = implode(',', array_fill(0, count($this->attributes), '?'));

        $stmt = $this->db->prepare(
            "INSERT INTO {$this->table} ({$columns})
             VALUES ({$placeholders})"
        );

        $result = $stmt->execute(array_values($this->attributes));

        if ($result) {
            $this->attributes['id'] = (int) $this->db->lastInsertId();
            $this->fireModelEvent('created', false);
        }

        return $result;
    }

    /**
     * Delete a record.
     */
    public function delete(): bool
    {
        if ($this->fireModelEvent('deleting') === false) {
            return false;
        }

        if (isset($this->attributes['id'])) {
            $result = $this->performDeleteOnModel();
            if ($result) {
                $this->fireModelEvent('deleted', false);
            }
            return $result;
        }

        return false;
    }

    /**
     * Reload a fresh model instance from the database.
     *
     * @param  array|string  $with
     * @return static|null
     */
    public function fresh($with = []): ?static
    {
        if (! isset($this->attributes['id'])) {
            return null;
        }

        return static::find($this->attributes['id']);
    }

    /**
     * Reload the current model instance with fresh attributes from the database.
     *
     * @return $this
     */
    public function refresh(): static
    {
        if (! isset($this->attributes['id'])) {
            return $this;
        }

        $this->fill(static::find($this->attributes['id'])->attributes);

        return $this;
    }

    /**
     * Clone the model into a new, non-existing instance.
     *
     * @param  array|null  $except
     * @return static
     */
    public function replicate(?array $except = null): static
    {
        $attributes = $this->attributes;

        unset($attributes['id']);

        if (! is_null($except)) {
            foreach ($except as $key) {
                unset($attributes[$key]);
            }
        }

        return new static($attributes);
    }

    /**
     * Update the model's update timestamp.
     *
     * @return bool
     */
    public function touch(): bool
    {
        if (! $this->timestamps) {
            return false;
        }

        $this->updateTimestamps();

        return $this->save();
    }

    /**
     * Increment a column's value by a given amount.
     *
     * @param  string  $column
     * @param  int  $amount
     * @return int
     */
    public function increment(string $column, int $amount = 1): int
    {
        return $this->incrementOrDecrement($column, $amount, 'increment');
    }

    /**
     * Decrement a column's value by a given amount.
     *
     * @param  string  $column
     * @param  int  $amount
     * @return int
     */
    public function decrement(string $column, int $amount = 1): int
    {
        return $this->incrementOrDecrement($column, $amount, 'decrement');
    }

    /**
     * Run an increment or decrement statement on the model.
     *
     * @param  string  $column
     * @param  int  $amount
     * @param  string  $method
     * @return int
     */
    protected function incrementOrDecrement(string $column, int $amount, string $method): int
    {
        $this->{$column} = $this->{$column} + ($method === 'increment' ? $amount : -$amount);

        $this->save();

        return $this->{$column};
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->attributes[$offset]);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->getAttribute($offset);
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->setAttribute($offset, $value);
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->attributes[$offset]);
    }

    /**
     * Handle dynamic method calls into the model.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return static::query()->$method(...$parameters);
    }

    public static function __callStatic($method, $parameters)
    {
        return (new static)->$method(...$parameters);
    }
}
