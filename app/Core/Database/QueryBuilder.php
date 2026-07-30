<?php

declare(strict_types=1);

namespace App\Core\Database;

use App\Core\ORM\Collection;
use App\Core\ORM\Exceptions\ModelNotFoundException;
use App\Core\ORM\Pagination\Paginator;
use PDO;
use PDOStatement;

/**
 * Fluent SQL query builder for the ZeroPing ORM.
 *
 * Builds SELECT, INSERT, UPDATE, and DELETE queries using a safe identifier
 * validation layer ({@see Identifier}) and PDO parameter binding. Supports
 * soft-delete awareness, pagination, aggregates, and model hydration.
 *
 * Instances are typically obtained via `Model::query()` or `Model::newQuery()`
 * rather than being constructed directly.
 */
class QueryBuilder
{
    /** The PDO connection. */
    protected PDO $db;

    /** The quoted table name. */
    protected string $table;

    /** @var list<string> Columns to select. */
    protected array $columns = ['*'];

    /** @var list<string> WHERE clause fragments. */
    protected array $where = [];

    /** @var list<mixed> Bound parameter values. */
    protected array $bindings = [];

    /** @var list<string> ORDER BY fragments. */
    protected array $orderBy = [];

    /** @var list<string> GROUP BY columns. */
    protected array $groupBy = [];

    /** @var list<string> HAVING clause fragments. */
    protected array $having = [];

    /** @var list<string> JOIN clause fragments. */
    protected array $joins = [];

    /** Maximum rows to return. */
    protected ?int $limit = null;

    /** Rows to skip. */
    protected ?int $offset = null;

    /** Whether to add `deleted_at IS NULL` automatically. */
    protected bool $softDeletes = false;

    /** @var class-string|null Model class to hydrate results into. */
    protected ?string $modelClass = null;

    /**
     * @param  PDO     $db     Active PDO connection.
     * @param  string  $table  Raw table name (will be validated).
     */
    public function __construct(PDO $db, string $table)
    {
        $this->db = $db;
        $this->table = Identifier::table($table);
    }

    /**
     * Set the model class for result hydration.
     *
     * @param  class-string  $modelClass
     * @return $this
     */
    public function setModelClass(string $modelClass): static
    {
        $this->modelClass = $modelClass;

        return $this;
    }

    // -------------------------------------------------------------------------
    // SELECT
    // -------------------------------------------------------------------------

    /**
     * Set the columns to be selected.
     *
     * @param  string|list<string>  $columns
     * @return $this
     */
    public function select(string|array $columns = ['*']): static
    {
        $columns = is_array($columns) ? $columns : func_get_args();
        $this->columns = array_map(
            static fn(mixed $column): string => Identifier::column((string) $column, true),
            $columns
        );

        return $this;
    }

    // -------------------------------------------------------------------------
    // WHERE clauses
    // -------------------------------------------------------------------------

    /**
     * Add a basic WHERE condition.
     *
     * @param  string       $column    Column name.
     * @param  mixed        $value     Value to compare against.
     * @param  string|null  $operator  Comparison operator (defaults to '=').
     * @return $this
     */
    public function where(string $column, mixed $value, ?string $operator = null): static
    {
        $this->where[] = Identifier::column($column) . ' ' . Identifier::operator($operator ?? '=') . ' ?';
        $this->bindings[] = $value;

        return $this;
    }

    /**
     * Add an OR WHERE condition.
     *
     * @return $this
     */
    public function orWhere(string $column, mixed $value, ?string $operator = null): static
    {
        if ($this->where === []) {
            return $this->where($column, $value, $operator);
        }

        $this->where[] = 'OR ' . Identifier::column($column) . ' ' . Identifier::operator($operator ?? '=') . ' ?';
        $this->bindings[] = $value;

        return $this;
    }

    /**
     * Add a WHERE IN condition.
     *
     * @param  string       $column
     * @param  list<mixed>  $values
     * @return $this
     */
    public function whereIn(string $column, array $values): static
    {
        $col = Identifier::column($column);

        if ($values === []) {
            // Empty set – always false
            $this->where[] = '0 = 1';

            return $this;
        }

        $placeholders = implode(',', array_fill(0, count($values), '?'));
        $this->where[] = $col . ' IN (' . $placeholders . ')';
        $this->bindings = [...$this->bindings, ...$values];

        return $this;
    }

    /**
     * Add a WHERE column IS NULL condition.
     *
     * @return $this
     */
    public function whereNull(string $column): static
    {
        $this->where[] = Identifier::column($column) . ' IS NULL';

        return $this;
    }

    /**
     * Add a WHERE column IS NOT NULL condition.
     *
     * @return $this
     */
    public function whereNotNull(string $column): static
    {
        $this->where[] = Identifier::column($column) . ' IS NOT NULL';

        return $this;
    }

    // -------------------------------------------------------------------------
    // JOINs
    // -------------------------------------------------------------------------

    /**
     * Add a JOIN clause.
     *
     * @param  string  $table     Table to join.
     * @param  string  $first     Left column.
     * @param  string  $operator  Join operator (=, !=, etc.).
     * @param  string  $second    Right column.
     * @param  string  $type      Join type: INNER, LEFT, or RIGHT.
     * @return $this
     */
    public function join(string $table, string $first, string $operator, string $second, string $type = 'INNER'): static
    {
        $this->joins[] = sprintf(
            '%s JOIN %s ON %s %s %s',
            Identifier::joinType($type),
            Identifier::table($table),
            Identifier::column($first),
            Identifier::operator($operator),
            Identifier::column($second)
        );

        return $this;
    }

    /**
     * Add a LEFT JOIN clause.
     *
     * @return $this
     */
    public function leftJoin(string $table, string $first, string $operator, string $second): static
    {
        return $this->join($table, $first, $operator, $second, 'LEFT');
    }

    /**
     * Add a RIGHT JOIN clause.
     *
     * @return $this
     */
    public function rightJoin(string $table, string $first, string $operator, string $second): static
    {
        return $this->join($table, $first, $operator, $second, 'RIGHT');
    }

    // -------------------------------------------------------------------------
    // ORDER BY
    // -------------------------------------------------------------------------

    /**
     * Add an ORDER BY clause.
     *
     * @param  string  $column     Column to sort by.
     * @param  string  $direction  ASC or DESC.
     * @return $this
     */
    public function orderBy(string $column, string $direction = 'ASC'): static
    {
        $direction = strtoupper($direction);
        if (!in_array($direction, ['ASC', 'DESC'], true)) {
            $direction = 'ASC';
        }

        $this->orderBy[] = Identifier::column($column) . ' ' . $direction;

        return $this;
    }

    /**
     * Order by the given column descending (newest first).
     *
     * @return $this
     */
    public function latest(string $column = 'created_at'): static
    {
        return $this->orderBy($column, 'DESC');
    }

    /**
     * Order by the given column ascending (oldest first).
     *
     * @return $this
     */
    public function oldest(string $column = 'created_at'): static
    {
        return $this->orderBy($column, 'ASC');
    }

    // -------------------------------------------------------------------------
    // GROUP BY / HAVING
    // -------------------------------------------------------------------------

    /**
     * Add a GROUP BY clause.
     *
     * @param  string|list<string>  $columns
     * @return $this
     */
    public function groupBy(string|array $columns): static
    {
        $columns = is_array($columns) ? $columns : func_get_args();
        $this->groupBy = array_map(
            static fn(mixed $column): string => Identifier::column((string) $column),
            $columns
        );

        return $this;
    }

    /**
     * Add a HAVING clause.
     *
     * @return $this
     */
    public function having(string $column, string $operator, mixed $value): static
    {
        $this->having[] = Identifier::column($column) . ' ' . Identifier::operator($operator) . ' ?';
        $this->bindings[] = $value;

        return $this;
    }

    // -------------------------------------------------------------------------
    // LIMIT / OFFSET
    // -------------------------------------------------------------------------

    /**
     * Set a maximum number of rows to return.
     *
     * @return $this
     *
     * @throws \InvalidArgumentException If limit is negative.
     */
    public function limit(int $limit): static
    {
        if ($limit < 0) {
            throw new \InvalidArgumentException('Limit must be zero or greater.');
        }

        $this->limit = $limit;

        return $this;
    }

    /**
     * Set the number of rows to skip.
     *
     * @return $this
     *
     * @throws \InvalidArgumentException If offset is negative.
     */
    public function offset(int $offset): static
    {
        if ($offset < 0) {
            throw new \InvalidArgumentException('Offset must be zero or greater.');
        }

        $this->offset = $offset;

        return $this;
    }

    /**
     * Alias of limit().
     *
     * @return $this
     */
    public function take(int $value): static
    {
        return $this->limit($value);
    }

    /**
     * Alias of offset().
     *
     * @return $this
     */
    public function skip(int $value): static
    {
        return $this->offset($value);
    }

    // -------------------------------------------------------------------------
    // Fetching Results
    // -------------------------------------------------------------------------

    /**
     * Execute the query and return all results as a Collection.
     *
     * When a model class is set, rows are hydrated into model instances
     * using forceFill() to bypass mass-assignment guards.
     */
    public function get(): Collection
    {
        $stmt = $this->db->prepare($this->toSql());
        $stmt->execute($this->bindings);
        $rows = $stmt->fetchAll();
        $this->reset();

        if ($this->modelClass !== null) {
            $modelClass = $this->modelClass;
            $rows = array_map(static function (array $attributes) use ($modelClass): object {
                $model = new $modelClass();
                $model->forceFill($attributes);

                return $model;
            }, $rows);
        }

        return new Collection($rows);
    }

    /**
     * Get the first result, or null.
     *
     * @param  string|list<string>|null  $columns  Override selected columns.
     */
    public function first(string|array|null $columns = null): mixed
    {
        if ($columns !== null) {
            $this->select($columns);
        }

        $this->limit(1);

        $rows = $this->get();

        return $rows[0] ?? null;
    }

    /**
     * Get the first result or throw a ModelNotFoundException.
     *
     * @param  string|list<string>|null  $columns
     *
     * @throws ModelNotFoundException
     */
    public function firstOrFail(string|array|null $columns = null): mixed
    {
        $result = $this->first($columns);

        if ($result === null) {
            throw new ModelNotFoundException();
        }

        return $result;
    }

    /**
     * Find a record by its primary key.
     *
     * @param  mixed               $id       Primary key value.
     * @param  string|list<string> $columns  Columns to select.
     */
    public function find(mixed $id, string|array $columns = ['*']): mixed
    {
        return $this->where('id', $id)->first($columns);
    }

    /**
     * Find a record by primary key or throw.
     *
     * @throws ModelNotFoundException
     */
    public function findOrFail(mixed $id, string|array $columns = ['*']): mixed
    {
        $result = $this->find($id, $columns);

        if ($result === null) {
            throw new ModelNotFoundException();
        }

        return $result;
    }

    // -------------------------------------------------------------------------
    // Aggregates
    // -------------------------------------------------------------------------

    /**
     * Get the count of matching rows.
     */
    public function count(): int
    {
        $columns = $this->columns;
        $this->columns = ['COUNT(*) as aggregate'];

        $stmt = $this->db->prepare($this->toSql());
        $stmt->execute($this->bindings);
        $result = (int) $stmt->fetchColumn();

        $this->columns = $columns;
        $this->reset();

        return $result;
    }

    /**
     * Determine if any rows exist for the query.
     */
    public function exists(): bool
    {
        $this->limit(1);
        $this->columns = ['1 as exists_result'];

        $stmt = $this->db->prepare($this->toSql());
        $stmt->execute($this->bindings);
        $result = $stmt->fetchColumn() !== false;
        $this->reset();

        return $result;
    }

    /**
     * Get the sum of a column.
     */
    public function sum(string $column): mixed
    {
        return $this->aggregate('SUM', $column);
    }

    /**
     * Get the average of a column.
     */
    public function avg(string $column): mixed
    {
        return $this->aggregate('AVG', $column);
    }

    /**
     * Get the maximum value of a column.
     */
    public function max(string $column): mixed
    {
        return $this->aggregate('MAX', $column);
    }

    /**
     * Get the minimum value of a column.
     */
    public function min(string $column): mixed
    {
        return $this->aggregate('MIN', $column);
    }

    /**
     * Execute an aggregate function.
     */
    protected function aggregate(string $function, string $column): mixed
    {
        $this->columns = [$function . '(' . Identifier::column($column) . ') as aggregate'];
        $result = $this->first();

        return $result['aggregate'] ?? null;
    }

    // -------------------------------------------------------------------------
    // INSERT / UPDATE / DELETE
    // -------------------------------------------------------------------------

    /**
     * Insert a row into the table.
     *
     * @param  array<string, mixed>  $data
     *
     * @throws \InvalidArgumentException If data is empty.
     */
    public function insert(array $data): bool
    {
        if ($data === []) {
            throw new \InvalidArgumentException('Insert data must not be empty.');
        }

        $columns = array_map(
            static fn(mixed $column): string => Identifier::column((string) $column),
            array_keys($data)
        );

        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        $sql = 'INSERT INTO ' . $this->table
            . ' (' . implode(', ', $columns) . ')'
            . ' VALUES (' . $placeholders . ')';

        return $this->db->prepare($sql)->execute(array_values($data));
    }

    /**
     * Update rows matching the current WHERE conditions.
     *
     * @param  array<string, mixed>  $data
     *
     * @throws \InvalidArgumentException If data is empty.
     */
    public function update(array $data): bool
    {
        if ($data === []) {
            throw new \InvalidArgumentException('Update data must not be empty.');
        }

        $set = [];
        foreach (array_keys($data) as $column) {
            $set[] = Identifier::column((string) $column) . ' = ?';
        }

        $sql = 'UPDATE ' . $this->table
            . ' SET ' . implode(', ', $set)
            . $this->compileWhereClause();

        $result = $this->db->prepare($sql)->execute([...array_values($data), ...$this->bindings]);
        $this->reset();

        return $result;
    }

    /**
     * Delete rows matching the current WHERE conditions.
     *
     * This performs a hard delete regardless of soft-delete settings.
     */
    public function delete(): bool
    {
        $sql = 'DELETE FROM ' . $this->table . $this->compileWhereClause();
        $result = $this->db->prepare($sql)->execute($this->bindings);
        $this->reset();

        return $result;
    }

    /**
     * Force-delete rows (same as delete, explicit name for soft-delete models).
     */
    public function forceDelete(): bool
    {
        return $this->delete();
    }

    // -------------------------------------------------------------------------
    // Pagination
    // -------------------------------------------------------------------------

    /**
     * Paginate results with total count.
     *
     * @throws \InvalidArgumentException If pagination values are not positive.
     */
    public function paginate(int $perPage = 15, int $currentPage = 1): Paginator
    {
        if ($perPage < 1 || $currentPage < 1) {
            throw new \InvalidArgumentException('Pagination values must be positive integers.');
        }

        $total = $this->count();
        $items = $this->limit($perPage)->offset(($currentPage - 1) * $perPage)->get();

        return new Paginator($items, $total, $perPage, $currentPage);
    }

    /**
     * Simple pagination without total count (more efficient for large tables).
     *
     * @throws \InvalidArgumentException If pagination values are not positive.
     */
    public function simplePaginate(int $perPage = 15, int $currentPage = 1): Paginator
    {
        if ($perPage < 1 || $currentPage < 1) {
            throw new \InvalidArgumentException('Pagination values must be positive integers.');
        }

        $items = $this->limit($perPage + 1)->offset(($currentPage - 1) * $perPage)->get();

        if (count($items) > $perPage) {
            $items->pop();
        }

        return new Paginator($items, 0, $perPage, $currentPage);
    }

    // -------------------------------------------------------------------------
    // Soft Delete Awareness
    // -------------------------------------------------------------------------

    /**
     * Enable soft-delete filtering (exclude trashed rows).
     *
     * @return $this
     */
    public function softDeletes(): static
    {
        $this->softDeletes = true;

        return $this;
    }

    /**
     * Disable soft-delete filtering (include trashed rows).
     *
     * @return $this
     */
    public function withTrashed(): static
    {
        $this->softDeletes = false;

        return $this;
    }

    /**
     * Only include soft-deleted (trashed) rows.
     *
     * @return $this
     */
    public function onlyTrashed(): static
    {
        $this->softDeletes = false;
        $this->where[] = 'deleted_at IS NOT NULL';

        return $this;
    }

    // -------------------------------------------------------------------------
    // SQL Compilation
    // -------------------------------------------------------------------------

    /**
     * Compile the query into a raw SQL string.
     *
     * Note: Bindings are NOT interpolated — use getBindings() for execution.
     */
    public function toSql(): string
    {
        $sql = 'SELECT ' . implode(', ', $this->columns) . ' FROM ' . $this->table;

        if ($this->joins !== []) {
            $sql .= ' ' . implode(' ', $this->joins);
        }

        $conditions = $this->where;
        if ($this->softDeletes) {
            $conditions[] = 'deleted_at IS NULL';
        }

        if ($conditions !== []) {
            $sql .= $this->compileConditions($conditions, ' WHERE ');
        }

        if ($this->groupBy !== []) {
            $sql .= ' GROUP BY ' . implode(', ', $this->groupBy);
        }

        if ($this->having !== []) {
            $sql .= ' HAVING ' . implode(' AND ', $this->having);
        }

        if ($this->orderBy !== []) {
            $sql .= ' ORDER BY ' . implode(', ', $this->orderBy);
        }

        if ($this->limit !== null) {
            $sql .= ' LIMIT ' . $this->limit;
        }

        if ($this->offset !== null) {
            $sql .= ' OFFSET ' . $this->offset;
        }

        return $sql;
    }

    /**
     * Get the current query bindings.
     *
     * @return list<mixed>
     */
    public function getBindings(): array
    {
        return $this->bindings;
    }

    /**
     * Reset the builder state for reuse.
     *
     * @return $this
     */
    public function reset(): static
    {
        $this->columns = ['*'];
        $this->where = [];
        $this->bindings = [];
        $this->orderBy = [];
        $this->groupBy = [];
        $this->having = [];
        $this->joins = [];
        $this->limit = null;
        $this->offset = null;
        $this->softDeletes = false;

        return $this;
    }

    // -------------------------------------------------------------------------
    // Internal Helpers
    // -------------------------------------------------------------------------

    /**
     * Compile the WHERE clause from current conditions.
     */
    private function compileWhereClause(): string
    {
        return $this->where === [] ? '' : $this->compileConditions($this->where, ' WHERE ');
    }

    /**
     * Compile a list of SQL conditions into a single clause string.
     *
     * Handles AND/OR prefixing: the first condition never starts with a
     * conjunction; subsequent conditions default to AND if not prefixed.
     *
     * @param  list<string>  $conditions
     * @param  string        $prefix  e.g. ' WHERE ' or ' HAVING '
     */
    private function compileConditions(array $conditions, string $prefix): string
    {
        $compiled = [];

        foreach ($conditions as $index => $condition) {
            $condition = trim($condition);

            if ($index === 0) {
                // Strip leading AND/OR from the first condition
                $condition = preg_replace('/^(AND|OR)\s+/i', '', $condition) ?? $condition;
            } elseif (preg_match('/^(AND|OR)\s+/i', $condition) !== 1) {
                // Default to AND if no conjunction is present
                $condition = 'AND ' . $condition;
            }

            $compiled[] = $condition;
        }

        return $prefix . implode(' ', $compiled);
    }
}
