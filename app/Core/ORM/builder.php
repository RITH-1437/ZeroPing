<?php

namespace App\Core\ORM;

class Builder
{
    /**
     * Model instance.
     */
    protected Model $model;

    /**
     * Table name.
     */
    protected string $table;

    /**
     * Query clauses.
     */
    protected array $wheres = [];

    protected array $orders = [];

    protected ?int $limit = null;

    public function __construct(Model $model)
    {
        $this->model = $model;

        $this->table = $model->getTable();
    }

    /**
     * Add where clause.
     */
    public function where(
        string $column,
        string $operator,
        mixed $value = null
    ): static {

        if (func_num_args() === 2) {
            $value = $operator;
            $operator = '=';
        }

        $this->wheres[] = [

            'column' => $column,

            'operator' => $operator,

            'value' => $value,

        ];

        return $this;
    }

    /**
     * Order results.
     */
    public function orderBy(
        string $column,
        string $direction = 'ASC'
    ): static {

        $this->orders[] = [

            'column' => $column,

            'direction' => strtoupper($direction),

        ];

        return $this;
    }

    /**
     * Limit results.
     */
    public function limit(int $limit): static
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * Get model.
     */
    public function getModel(): Model
    {
        return $this->model;
    }

    /**
     * Get table.
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * Get where clauses.
     */
    public function getWheres(): array
    {
        return $this->wheres;
    }

    /**
     * Get order clauses.
     */
    public function getOrders(): array
    {
        return $this->orders;
    }

    /**
     * Get limit.
     */
    public function getLimit(): ?int
    {
        return $this->limit;
    }
}