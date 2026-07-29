<?php

declare(strict_types=1);

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
    protected array $joins = [];
    protected ?int $limit = null;
    protected ?int $offset = null;
    protected bool $softDeletes = false;
    protected ?string $modelClass = null;

    public function __construct(PDO $db, string $table)
    {
        $this->db = $db;
        $this->table = Identifier::table($table);
    }

    public function setModelClass(string $modelClass): static
    {
        $this->modelClass = $modelClass;
        return $this;
    }

    /** @param string|list<string> $columns */
    public function select(string|array $columns = ['*']): static
    {
        $columns = is_array($columns) ? $columns : func_get_args();
        $this->columns = array_map(
            static fn (mixed $column): string => Identifier::column((string) $column, true),
            $columns
        );

        return $this;
    }

    public function where(string $column, mixed $value, ?string $operator = null): static
    {
        $this->where[] = Identifier::column($column) . ' ' . Identifier::operator($operator ?? '=') . ' ?';
        $this->bindings[] = $value;

        return $this;
    }

    public function orWhere(string $column, mixed $value, ?string $operator = null): static
    {
        if ($this->where === []) {
            return $this->where($column, $value, $operator);
        }

        $this->where[] = 'OR ' . Identifier::column($column) . ' ' . Identifier::operator($operator ?? '=') . ' ?';
        $this->bindings[] = $value;

        return $this;
    }

    public function whereIn(string $column, array $values): static
    {
        $column = Identifier::column($column);
        if ($values === []) {
            $this->where[] = '0 = 1';
            return $this;
        }

        $this->where[] = $column . ' IN (' . implode(',', array_fill(0, count($values), '?')) . ')';
        $this->bindings = array_merge($this->bindings, $values);

        return $this;
    }

    public function whereNull(string $column): static
    {
        $this->where[] = Identifier::column($column) . ' IS NULL';
        return $this;
    }

    public function whereNotNull(string $column): static
    {
        $this->where[] = Identifier::column($column) . ' IS NOT NULL';
        return $this;
    }

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

    public function leftJoin(string $table, string $first, string $operator, string $second): static
    {
        return $this->join($table, $first, $operator, $second, 'LEFT');
    }

    public function rightJoin(string $table, string $first, string $operator, string $second): static
    {
        return $this->join($table, $first, $operator, $second, 'RIGHT');
    }

    public function orderBy(string $column, string $direction = 'ASC'): static
    {
        $direction = strtoupper($direction);
        if (!in_array($direction, ['ASC', 'DESC'], true)) {
            $direction = 'ASC';
        }

        $this->orderBy[] = Identifier::column($column) . " {$direction}";
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

    /** @param string|list<string> $columns */
    public function groupBy(string|array $columns): static
    {
        $columns = is_array($columns) ? $columns : func_get_args();
        $this->groupBy = array_map(
            static fn (mixed $column): string => Identifier::column((string) $column),
            $columns
        );

        return $this;
    }

    public function having(string $column, string $operator, mixed $value): static
    {
        $this->having[] = Identifier::column($column) . ' ' . Identifier::operator($operator) . ' ?';
        $this->bindings[] = $value;

        return $this;
    }

    public function limit(int $limit): static
    {
        if ($limit < 0) {
            throw new \InvalidArgumentException('Limit must be zero or greater.');
        }

        $this->limit = $limit;
        return $this;
    }

    public function offset(int $offset): static
    {
        if ($offset < 0) {
            throw new \InvalidArgumentException('Offset must be zero or greater.');
        }

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

    public function get(): Collection
    {
        $stmt = $this->db->prepare($this->toSql());
        $stmt->execute($this->bindings);
        $rows = $stmt->fetchAll();
        $this->reset();

        if ($this->modelClass !== null) {
            $rows = array_map(function (array $attributes): object {
                $model = new $this->modelClass();
                $model->forceFill($attributes);
                return $model;
            }, $rows);
        }

        return new Collection($rows);
    }

    /** @param string|list<string>|null $columns */
    public function first(string|array|null $columns = null): mixed
    {
        if ($columns !== null) {
            $this->select($columns);
        }
        $this->limit(1);
        $rows = $this->get();

        return $rows[0] ?? null;
    }

    /** @param string|list<string>|null $columns */
    public function firstOrFail(string|array|null $columns = null): mixed
    {
        $result = $this->first($columns);
        if ($result === null) {
            throw new ModelNotFoundException();
        }

        return $result;
    }

    /** @param string|list<string> $columns */
    public function find(mixed $id, string|array $columns = ['*']): mixed
    {
        return $this->where('id', $id)->first($columns);
    }

    /** @param string|list<string> $columns */
    public function findOrFail(mixed $id, string|array $columns = ['*']): mixed
    {
        $result = $this->find($id, $columns);
        if ($result === null) {
            throw new ModelNotFoundException();
        }

        return $result;
    }

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

    public function sum(string $column): mixed
    {
        return $this->aggregate('SUM', $column);
    }
    public function avg(string $column): mixed
    {
        return $this->aggregate('AVG', $column);
    }
    public function max(string $column): mixed
    {
        return $this->aggregate('MAX', $column);
    }
    public function min(string $column): mixed
    {
        return $this->aggregate('MIN', $column);
    }

    protected function aggregate(string $function, string $column): mixed
    {
        $this->columns = [$function . '(' . Identifier::column($column) . ') as aggregate'];
        $result = $this->first();

        return $result['aggregate'] ?? null;
    }

    public function insert(array $data): bool
    {
        if ($data === []) {
            throw new \InvalidArgumentException('Insert data must not be empty.');
        }

        $columns = array_map(
            static fn (mixed $column): string => Identifier::column((string) $column),
            array_keys($data)
        );
        $sql = 'INSERT INTO ' . $this->table . ' (' . implode(', ', $columns) . ') VALUES ('
            . implode(', ', array_fill(0, count($data), '?')) . ')';

        return $this->db->prepare($sql)->execute(array_values($data));
    }

    public function update(array $data): bool
    {
        if ($data === []) {
            throw new \InvalidArgumentException('Update data must not be empty.');
        }

        $set = [];
        foreach (array_keys($data) as $column) {
            $set[] = Identifier::column((string) $column) . ' = ?';
        }

        $sql = 'UPDATE ' . $this->table . ' SET ' . implode(', ', $set) . $this->compileWhereClause();
        $result = $this->db->prepare($sql)->execute([...array_values($data), ...$this->bindings]);
        $this->reset();

        return $result;
    }

    public function delete(): bool
    {
        $result = $this->db->prepare('DELETE FROM ' . $this->table . $this->compileWhereClause())
            ->execute($this->bindings);
        $this->reset();

        return $result;
    }

    public function forceDelete(): bool
    {
        return $this->delete();
    }

    public function paginate(int $perPage = 15, int $currentPage = 1): Paginator
    {
        if ($perPage < 1 || $currentPage < 1) {
            throw new \InvalidArgumentException('Pagination values must be positive integers.');
        }

        $total = $this->count();
        $items = $this->limit($perPage)->offset(($currentPage - 1) * $perPage)->get();

        return new Paginator($items, $total, $perPage, $currentPage);
    }

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

    public function softDeletes(): static
    {
        $this->softDeletes = true;
        return $this;
    }

    public function withTrashed(): static
    {
        $this->softDeletes = false;
        return $this;
    }

    public function onlyTrashed(): static
    {
        $this->softDeletes = false;
        $this->where[] = 'deleted_at IS NOT NULL';
        return $this;
    }

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

    private function compileWhereClause(): string
    {
        return $this->where === [] ? '' : $this->compileConditions($this->where, ' WHERE ');
    }

    /** @param list<string> $conditions */
    private function compileConditions(array $conditions, string $prefix): string
    {
        $compiled = [];
        foreach ($conditions as $index => $condition) {
            $condition = trim($condition);
            if ($index === 0) {
                $condition = preg_replace('/^(AND|OR)\s+/i', '', $condition) ?? $condition;
            } elseif (preg_match('/^(AND|OR)\s+/i', $condition) !== 1) {
                $condition = 'AND ' . $condition;
            }
            $compiled[] = $condition;
        }

        return $prefix . implode(' ', $compiled);
    }
}
