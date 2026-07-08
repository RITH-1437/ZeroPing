<?php

namespace App\Core\Database;

use App\Core\ORM\Collection;
use App\Core\ORM\Exceptions\ModelNotFoundException;
use App\Core\ORM\Pagination\Paginator;
use PDO;

class QueryBuilder
{
    protected PDO $db;

    protected string $table;

    protected array $columns = ['*'];

    protected array $where = [];

    protected array $bindings = [];

    protected array $orderBy = [];

    protected array $groupBy = [];

    protected array $having = [];

    protected ?int $limit = null;

    protected ?int $offset = null;

    protected bool $softDeletes = false;

    public function __construct(PDO $db, string $table)
    {
        $this->db = $db;
        $this->table = $table;
    }

    /*
    |--------------------------------------------------------------------------
    | Query Constraints
    |--------------------------------------------------------------------------
    */

    public function select($columns = ['*']): static
    {
        $this->columns = is_array($columns) ? $columns : func_get_args();

        return $this;
    }

    public function where(string $column, mixed $value): static
    {
        $this->where[] = "{$column} = ?";

        $this->bindings[] = $value;

        return $this;
    }

    public function orWhere(string $column, mixed $value): static
    {
        if (empty($this->where)) {
            return $this->where($column, $value);
        }

        $this->where[] = "OR {$column} = ?";

        $this->bindings[] = $value;

        return $this;
    }

    public function whereIn(string $column, array $values): static
    {
        $placeholders = implode(',', array_fill(0, count($values), '?'));

        $this->where[] = "{$column} IN ({$placeholders})";

        $this->bindings = array_merge($this->bindings, $values);

        return $this;
    }

    public function whereNull(string $column): static
    {
        $this->where[] = "{$column} IS NULL";

        return $this;
    }

    public function whereNotNull(string $column): static
    {
        $this->where[] = "{$column} IS NOT NULL";

        return $this;
    }

    public function orderBy(
        string $column,
        string $direction = 'ASC'
    ): static {

        $direction = strtoupper($direction);

        if (!in_array($direction, ['ASC', 'DESC'])) {
            $direction = 'ASC';
        }

        $this->orderBy[] = "{$column} {$direction}";

        return $this;
    }

    public function latest(string $column = 'created_at'): static
    {
        return $this->orderBy($column, 'DESC');
    }

    public function oldest(string $column = 'created_at'): static
    {
        return $this->orderBy($column, 'ASC');
    }

    public function groupBy($columns): static
    {
        $this->groupBy = is_array($columns) ? $columns : func_get_args();

        return $this;
    }

    public function having(string $column, string $operator, mixed $value): static
    {
        $this->having[] = "{$column} {$operator} ?";

        $this->bindings[] = $value;

        return $this;
    }

    public function limit(int $limit): static
    {
        $this->limit = $limit;

        return $this;
    }

    public function offset(int $offset): static
    {
        $this->offset = $offset;

        return $this;
    }

    public function take(int $value): static
    {
        return $this->limit($value);
    }

    public function skip(int $value): static
    {
        return $this->offset($value);
    }

    /*
    |--------------------------------------------------------------------------
    | Read Queries
    |--------------------------------------------------------------------------
    */

    public function get(): Collection
    {
        $stmt = $this->db->prepare($this->toSql());

        $stmt->execute($this->bindings);

        $rows = $stmt->fetchAll();

        $this->reset();

        return new Collection($rows);
    }

    public function first(): ?array
    {
        $this->limit(1);

        $rows = $this->get();

        return $rows[0] ?? null;
    }

    public function firstOrFail(): array
    {
        $result = $this->first();

        if (is_null($result)) {
            throw new ModelNotFoundException;
        }

        return $result;
    }

    public function find($id, $columns = ['*'])
    {
        return $this->where('id', $id)->first($columns);
    }

    public function findOrFail($id, $columns = ['*'])
    {
        $result = $this->find($id, $columns);

        if (is_null($result)) {
            throw new ModelNotFoundException;
        }

        return $result;
    }

    public function count(): int
    {
        return count($this->get());
    }

    public function exists(): bool
    {
        return $this->first() !== null;
    }

    public function sum($column)
    {
        return $this->aggregate(__FUNCTION__, $column);
    }

    public function avg($column)
    {
        return $this->aggregate(__FUNCTION__, $column);
    }

    public function max($column)
    {
        return $this->aggregate(__FUNCTION__, $column);
    }

    public function min($column)
    {
        return $this->aggregate(__FUNCTION__, $column);
    }

    protected function aggregate($function, $column)
    {
        $this->columns = ["{$function}({$column}) as aggregate"];

        $result = $this->first();

        return $result['aggregate'] ?? null;
    }

    /*
    |--------------------------------------------------------------------------
    | Write Queries
    |--------------------------------------------------------------------------
    */

    public function insert(array $data): bool
    {
        $columns = implode(', ', array_keys($data));

        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute(array_values($data));
    }

    public function update(array $data): bool
    {
        $set = [];

        $bindings = [];

        foreach ($data as $column => $value) {

            $set[] = "{$column} = ?";

            $bindings[] = $value;
        }

        $sql = "UPDATE {$this->table} SET ";

        $sql .= implode(', ', $set);

        if (!empty($this->where)) {

            $sql .= " WHERE ";

            $first = true;

            foreach ($this->where as $condition) {

                if ($first) {

                    $condition = preg_replace('/^OR /', '', $condition);

                    $first = false;
                }

                $sql .= $condition . " ";
            }
        }

        $stmt = $this->db->prepare($sql);

        $result = $stmt->execute([
            ...$bindings,
            ...$this->bindings
        ]);

        $this->reset();

        return $result;
    }

    public function delete(): bool
    {
        $sql = "DELETE FROM {$this->table}";

        if (!empty($this->where)) {

            $sql .= " WHERE ";

            $first = true;

            foreach ($this->where as $condition) {

                if ($first) {

                    $condition = preg_replace('/^OR /', '', $condition);

                    $first = false;
                }

                $sql .= $condition . " ";
            }
        }

        $stmt = $this->db->prepare($sql);

        $result = $stmt->execute($this->bindings);

        $this->reset();

        return $result;
    }

    public function forceDelete(): bool
    {
        return $this->delete();
    }

    /*
    |--------------------------------------------------------------------------
    | Pagination
    |--------------------------------------------------------------------------
    */

    public function paginate(int $perPage = 15, int $currentPage = 1): Paginator
    {
        $total = $this->count();

        $this->limit($perPage)->offset(($currentPage - 1) * $perPage);

        $items = $this->get();

        return new Paginator($items, $total, $perPage, $currentPage);
    }

    public function simplePaginate(int $perPage = 15, int $currentPage = 1): Paginator
    {
        $this->limit($perPage + 1)->offset(($currentPage - 1) * $perPage);

        $items = $this->get();

        $hasMore = count($items) > $perPage;

        if ($hasMore) {
            $items->pop();
        }

        return new Paginator($items, 0, $perPage, $currentPage);
    }

    /*
    |--------------------------------------------------------------------------
    | Soft Deletes
    |--------------------------------------------------------------------------
    */

    public function withTrashed(): static
    {
        $this->softDeletes = true;

        return $this;
    }

    public function onlyTrashed(): static
    {
        $this->softDeletes = true;

        $this->where[] = "deleted_at IS NOT NULL";

        return $this;
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function reset(): static
    {
        $this->columns = ['*'];
        $this->where = [];
        $this->bindings = [];
        $this->orderBy = [];
        $this->groupBy = [];
        $this->having = [];
        $this->limit = null;
        $this->offset = null;
        $this->softDeletes = false;

        return $this;
    }

    public function toSql(): string
    {
        $sql = "SELECT " . implode(', ', $this->columns) . " FROM {$this->table}";

        if ($this->softDeletes === false) {
            $this->where[] = "deleted_at IS NULL";
        }

        if (!empty($this->where)) {
            $sql .= " WHERE ";
            $first = true;

            foreach ($this->where as $condition) {
                if ($first) {
                    $condition = preg_replace('/^(AND |OR )/i', '', $condition);
                    $first = false;
                } else {
                    if (!preg_match('/^(AND |OR )/i', $condition)) {
                        $condition = "AND " . $condition;
                    }
                }
                $sql .= $condition . " ";
            }
        }

        if (!empty($this->groupBy)) {
            $sql .= " GROUP BY " . implode(', ', $this->groupBy);
        }

        if (!empty($this->having)) {
            $sql .= " HAVING " . implode(' AND ', $this->having);
        }

        if (!empty($this->orderBy)) {
            $sql .= " ORDER BY ";
            $sql .= implode(', ', $this->orderBy);
        }

        if ($this->limit !== null) {
            $sql .= " LIMIT {$this->limit}";
        }

        if ($this->offset !== null) {
            $sql .= " OFFSET {$this->offset}";
        }

        return trim($sql);
    }
}
