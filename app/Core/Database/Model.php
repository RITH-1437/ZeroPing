<?php

namespace App\Core\Database;

use App\Core\Database\QueryBuilder;
use App\Core\ORM\Concerns\GuardsAttributes;
use App\Core\ORM\Concerns\HasAttributes;
use App\Core\ORM\Concerns\HasEvents;
use App\Core\ORM\Concerns\HasRelationships;
use App\Core\ORM\Concerns\HasTimestamps;
use App\Core\ORM\Concerns\SoftDeletes;
use PDO;

abstract class Model
{
    use GuardsAttributes, HasTimestamps, HasAttributes, HasRelationships, SoftDeletes, HasEvents;

    protected PDO $db;

    protected string $table;

    public function __construct(array $attributes = [])
    {
        $this->db = Database::connect();
        $this->fill($attributes);
    }

    /**
     * Create a new query builder instance.
     */
    public function query(): QueryBuilder
    {
        return new QueryBuilder(
            $this->db,
            $this->table
        );
    }

    /**
     * Get all records.
     */
    public static function all(): array
    {
        return (new static)->query()->get();
    }

    /**
     * Find a record by primary key.
     */
    public static function find(int $id): ?static
    {
        $attributes = (new static)->query()
            ->where('id', $id)
            ->first();

        return $attributes ? new static($attributes) : null;
    }

    /**
     * Find first record by column.
     */
    public static function findBy(string $column, mixed $value): ?static
    {
        $attributes = (new static)->query()
            ->where($column, $value)
            ->first();

        return $attributes ? new static($attributes) : null;
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
            $result = $this->update();
        } else {
            $result = $this->insert();
        }

        if ($result) {
            $this->fireModelEvent('saved', false);
        }

        return $result;
    }

    /**
     * Update a record.
     */
    protected function update(): bool
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
    public function replicate(array $except = null): static
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

    /**
     * Handle dynamic method calls into the model.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->query()->$method(...$parameters);
    }

    /**
     * Handle dynamic static method calls into the model.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public static function __callStatic($method, $parameters)
    {
        return (new static)->$method(...$parameters);
    }
}
