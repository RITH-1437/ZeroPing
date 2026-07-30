<?php

declare(strict_types=1);

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

/**
 * Base model class for the ZeroPing ORM.
 *
 * Provides ActiveRecord-style persistence, query building, soft deletes,
 * timestamps, events, relationships, mass-assignment protection and
 * array-access to attributes.
 *
 * Concrete models should extend this class and set at minimum the `$table`
 * property and `$fillable` array.
 *
 * @example
 *   // Define a model:
 *   class Post extends Model {
 *       protected string $table = 'posts';
 *       protected array $fillable = ['title', 'body'];
 *   }
 *
 *   // Usage:
 *   $post = Post::create(['title' => 'Hello', 'body' => '...']);
 *   $all  = Post::where('published', true)->latest()->get();
 *
 * @since 1.0.0
 * @author Rin Nairith
 * @link https://zero-ping.duckdns.org/docs/database
 *
 * @method static QueryBuilder where(string $column, mixed $value, ?string $operator = null)
 * @method static QueryBuilder orWhere(string $column, mixed $value, ?string $operator = null)
 * @method static QueryBuilder whereIn(string $column, array $values)
 * @method static QueryBuilder whereNull(string $column)
 * @method static QueryBuilder whereNotNull(string $column)
 * @method static QueryBuilder orderBy(string $column, string $direction = 'ASC')
 * @method static QueryBuilder latest(string $column = 'created_at')
 * @method static QueryBuilder oldest(string $column = 'created_at')
 * @method static QueryBuilder limit(int $limit)
 * @method static QueryBuilder offset(int $offset)
 * @method static QueryBuilder select(string|array $columns = ['*'])
 * @method static QueryBuilder join(string $table, string $first, string $operator, string $second, string $type = 'INNER')
 * @method static QueryBuilder leftJoin(string $table, string $first, string $operator, string $second)
 * @method static QueryBuilder groupBy(string|array $columns)
 * @method static QueryBuilder having(string $column, string $operator, mixed $value)
 * @method static \App\Core\ORM\Pagination\Paginator paginate(int $perPage = 15, int $currentPage = 1)
 */
abstract class Model implements \ArrayAccess
{
    use GuardsAttributes;
    use HasTimestamps;
    use HasAttributes;
    use HasRelationships;
    use SoftDeletes;
    use HasEvents {
        HasRelationships::__get insteadof HasAttributes;
        HasAttributes::__get as getAttributeMagic;
    }

    /**
     * The PDO connection instance.
     */
    protected PDO $db;

    /**
     * The table associated with the model.
     */
    protected string $table;

    /**
     * The primary key column name.
     */
    protected string $primaryKey = 'id';

    /**
     * Set to false on models whose tables have no deleted_at column.
     */
    protected bool $hasSoftDeletes = true;

    /**
     * The registered model scopes.
     *
     * @var array<string, \Closure>
     */
    protected static array $scopes = [];

    /**
     * Create a new model instance.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->db = Database::connect();
        $this->fill($attributes);
    }

    /**
     * Get the primary key column name.
     */
    public function getKeyName(): string
    {
        return $this->primaryKey;
    }

    /**
     * Get the primary key value for this model instance.
     */
    public function getKey(): mixed
    {
        return $this->attributes[$this->primaryKey] ?? null;
    }

    /**
     * Get the table name for this model.
     */
    public function getTable(): string
    {
        return $this->table;
    }

    // -------------------------------------------------------------------------
    // Query Building
    // -------------------------------------------------------------------------

    /**
     * Create a new query builder instance for this model (static context).
     */
    public static function query(): QueryBuilder
    {
        $instance = new static();

        return $instance->newQuery();
    }

    /**
     * Create a new query builder instance (instance context).
     *
     * Use this when you already have a model instance and want to avoid
     * creating another one just to get a builder.
     */
    public function newQuery(): QueryBuilder
    {
        $qb = new QueryBuilder($this->db, $this->table);

        if ($this->hasSoftDeletes) {
            $qb->softDeletes();
        } else {
            $qb->withTrashed();
        }

        $qb->setModelClass(static::class);

        // Apply global scopes
        foreach (static::$scopes as $scope) {
            $scope($qb);
        }

        return $qb;
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    /**
     * Register a global scope on the model.
     *
     * Global scopes are applied to every query for this model class.
     *
     * @param  string    $name    Unique identifier for this scope.
     * @param  \Closure  $scope   Receives QueryBuilder, returns void.
     */
    public static function addGlobalScope(string $name, \Closure $scope): void
    {
        static::$scopes[$name] = $scope;
    }

    /**
     * Remove a registered global scope.
     */
    public static function removeGlobalScope(string $name): void
    {
        unset(static::$scopes[$name]);
    }

    /**
     * Clear all registered global scopes.
     */
    public static function clearGlobalScopes(): void
    {
        static::$scopes = [];
    }

    // -------------------------------------------------------------------------
    // CRUD Convenience Methods
    // -------------------------------------------------------------------------

    /**
     * Get all records.
     */
    public static function all(): Collection
    {
        return static::query()->get();
    }

    /**
     * Find a record by primary key.
     */
    public static function find(int|string $id): ?static
    {
        $instance = new static();

        return $instance->newQuery()
            ->where($instance->primaryKey, $id)
            ->first();
    }

    /**
     * Find first record by column value.
     */
    public static function findBy(string $column, mixed $value): ?static
    {
        return static::query()
            ->where($column, $value)
            ->first();
    }

    /**
     * Insert a new record and return the model.
     *
     * @param  array<string, mixed>  $attributes
     */
    public static function create(array $attributes = []): static
    {
        $model = new static($attributes);
        $model->save();

        return $model;
    }

    // -------------------------------------------------------------------------
    // Persistence
    // -------------------------------------------------------------------------

    /**
     * Save the model to the database (insert or update).
     */
    public function save(): bool
    {
        if ($this->fireModelEvent('saving') === false) {
            return false;
        }

        if ($this->timestamps) {
            $this->updateTimestamps();
        }

        $result = isset($this->attributes[$this->primaryKey])
            ? $this->performUpdate()
            : $this->performInsert();

        if ($result) {
            $this->fireModelEvent('saved', false);
        }

        return $result;
    }

    /**
     * Update the model with the given attributes and save.
     *
     * @param  array<string, mixed>  $attributes
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
        $pk = $this->primaryKey;

        foreach ($this->attributes as $column => $value) {
            if ($column !== $pk) {
                $fields[] = Identifier::column((string) $column) . ' = ?';
                $values[] = $value;
            }
        }

        if ($fields === []) {
            return true;
        }

        $values[] = $this->attributes[$pk];
        $stmt = $this->db->prepare(
            'UPDATE ' . Identifier::table($this->table)
            . ' SET ' . implode(', ', $fields)
            . ' WHERE ' . Identifier::column($pk) . ' = ?'
        );

        $result = $stmt->execute($values);

        if ($result) {
            $this->fireModelEvent('updated', false);
        }

        return $result;
    }

    /**
     * Perform the actual INSERT query.
     */
    protected function performInsert(): bool
    {
        if ($this->fireModelEvent('creating') === false) {
            return false;
        }

        if ($this->attributes === []) {
            throw new \InvalidArgumentException('Cannot insert an empty model.');
        }

        $columns = array_keys($this->attributes);
        $quotedColumns = array_map(
            static fn(string $col): string => Identifier::column($col),
            $columns
        );
        $placeholders = implode(', ', array_fill(0, count($columns), '?'));

        $sql = 'INSERT INTO ' . Identifier::table($this->table)
            . ' (' . implode(', ', $quotedColumns) . ')'
            . ' VALUES (' . $placeholders . ')';

        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute(array_values($this->attributes));

        if ($result) {
            $this->attributes[$this->primaryKey] = (int) $this->db->lastInsertId();
            $this->fireModelEvent('created', false);
        }

        return $result;
    }

    /**
     * Delete a record (soft-delete aware).
     */
    public function delete(): bool
    {
        if ($this->fireModelEvent('deleting') === false) {
            return false;
        }

        if (isset($this->attributes[$this->primaryKey])) {
            $result = $this->performDeleteOnModel();
            if ($result) {
                $this->fireModelEvent('deleted', false);
            }

            return $result;
        }

        return false;
    }

    // -------------------------------------------------------------------------
    // Model Utilities
    // -------------------------------------------------------------------------

    /**
     * Reload a fresh model instance from the database.
     *
     * @param  array|string  $with  Reserved for eager-loading (future).
     */
    public function fresh(array|string $with = []): ?static
    {
        if (!isset($this->attributes[$this->primaryKey])) {
            return null;
        }

        return static::find($this->attributes[$this->primaryKey]);
    }

    /**
     * Reload the current model instance with fresh attributes from the database.
     *
     * @return $this
     */
    public function refresh(): static
    {
        if (!isset($this->attributes[$this->primaryKey])) {
            return $this;
        }

        $fresh = static::find($this->attributes[$this->primaryKey]);

        if ($fresh !== null) {
            $this->forceFill($fresh->getAttributes());
        }

        return $this;
    }

    /**
     * Clone the model into a new, non-existing instance.
     *
     * @param  array<string>|null  $except  Attributes to exclude from the clone.
     */
    public function replicate(?array $except = null): static
    {
        $attributes = $this->attributes;
        unset($attributes[$this->primaryKey]);

        if ($except !== null) {
            foreach ($except as $key) {
                unset($attributes[$key]);
            }
        }

        return new static($attributes);
    }

    /**
     * Update the model's update timestamp without changing other attributes.
     */
    public function touch(): bool
    {
        if (!$this->timestamps) {
            return false;
        }

        $this->updateTimestamps();

        return $this->save();
    }

    /**
     * Increment a column's value by a given amount.
     */
    public function increment(string $column, int $amount = 1): int
    {
        return $this->incrementOrDecrement($column, $amount, 'increment');
    }

    /**
     * Decrement a column's value by a given amount.
     */
    public function decrement(string $column, int $amount = 1): int
    {
        return $this->incrementOrDecrement($column, $amount, 'decrement');
    }

    /**
     * Run an increment or decrement statement on the model.
     */
    protected function incrementOrDecrement(string $column, int $amount, string $method): int
    {
        $current = (int) ($this->attributes[$column] ?? 0);
        $this->attributes[$column] = $current + ($method === 'increment' ? $amount : -$amount);

        $this->save();

        return $this->attributes[$column];
    }

    // -------------------------------------------------------------------------
    // ArrayAccess Implementation
    // -------------------------------------------------------------------------

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

    // -------------------------------------------------------------------------
    // Magic Method Forwarding
    // -------------------------------------------------------------------------

    /**
     * Handle dynamic method calls into the model.
     *
     * Forwards unresolved instance method calls to a fresh QueryBuilder,
     * enabling `$model->where(...)` style chaining.
     *
     * @param  string  $method
     * @param  array<mixed>  $parameters
     * @return mixed
     */
    public function __call(string $method, array $parameters): mixed
    {
        // Check for local scope methods (scope{Name})
        $scopeMethod = 'scope' . ucfirst($method);
        if (method_exists($this, $scopeMethod)) {
            $query = $this->newQuery();
            $this->{$scopeMethod}($query, ...$parameters);

            return $query;
        }

        return $this->newQuery()->{$method}(...$parameters);
    }

    /**
     * Handle dynamic static method calls.
     *
     * Forwards to the query builder via a fresh model instance. Uses
     * newQuery() directly to avoid infinite recursion through __call.
     *
     * @param  string  $method
     * @param  array<mixed>  $parameters
     * @return mixed
     */
    public static function __callStatic(string $method, array $parameters): mixed
    {
        $instance = new static();

        // Check for local scope methods (scope{Name})
        $scopeMethod = 'scope' . ucfirst($method);
        if (method_exists($instance, $scopeMethod)) {
            $query = $instance->newQuery();
            $instance->{$scopeMethod}($query, ...$parameters);

            return $query;
        }

        return $instance->newQuery()->{$method}(...$parameters);
    }
}
