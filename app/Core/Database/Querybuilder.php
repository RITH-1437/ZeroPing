<?php

namespace App\Core\Database;

use PDO;

class QueryBuilder
{
    protected PDO $db;

    protected string $table;

    protected array $where = [];

    protected array $bindings = [];

    protected array $orderBy = [];

    protected ?int $limit = null;

    protected ?int $offset = null;

    public function __construct(PDO $db, string $table)
    {
        $this->db = $db;
        $this->table = $table;
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

    public function get(): array
    {
        $sql = "SELECT * FROM {$this->table}";

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

        $stmt = $this->db->prepare($sql);

        $stmt->execute($this->bindings);

        return $stmt->fetchAll();
    }

    public function first(): ?array
    {
        $this->limit(1);

        $rows = $this->get();

        return $rows[0] ?? null;
    }

    public function count(): int
    {
        return count($this->get());
    }

    public function exists(): bool
    {
        return $this->first() !== null;
    }
}