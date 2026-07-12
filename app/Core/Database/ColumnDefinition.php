<?php

namespace App\Core\Database;

class ColumnDefinition
{
    private string $sql;
    private bool $isNullable = false;
    private bool $hasDefault = false;
    private mixed $defaultValue = null;
    private bool $useCurrentDefault = false;

    public function __construct(string $sql)
    {
        $this->sql = $sql;
    }

    public function nullable(): static
    {
        $this->isNullable = true;
        return $this;
    }

    public function default(mixed $value): static
    {
        $this->hasDefault = true;
        $this->defaultValue = $value;
        return $this;
    }

    public function useCurrent(): static
    {
        $this->useCurrentDefault = true;
        return $this;
    }

    public function unsigned(): static
    {
        $this->sql .= ' UNSIGNED';
        return $this;
    }

    public function toSql(): string
    {
        $sql = $this->sql;

        if ($this->isNullable) {
            $sql .= ' NULL';
        } else {
            $sql .= ' NOT NULL';
        }

        if ($this->useCurrentDefault) {
            $sql .= ' DEFAULT CURRENT_TIMESTAMP';
        } elseif ($this->hasDefault) {
            $sql .= ' DEFAULT ' . (is_null($this->defaultValue) ? 'NULL' : "'" . $this->defaultValue . "'");
        }

        return $sql;
    }
}
