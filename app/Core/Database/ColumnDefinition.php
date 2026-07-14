<?php

namespace App\Core\Database;

/**
 * A portable description of a single table column.
 *
 * Unlike the previous class this one does NOT emit SQL. It only records the
 * column's intent (type, length, modifiers). The active Grammar turns it
 * into engine-specific SQL — so the same migration works on SQLite, MySQL,
 * MariaDB and PostgreSQL.
 */
class ColumnDefinition
{
    public function __construct(
        public string $name,
        public string $type,
        public ?int $length = null,
        public bool $unsigned = false,
        public bool $nullable = false,
        public bool $unique = false,
        public mixed $default = null,
        public bool $useCurrent = false,
        public array $values = [],
        public bool $primaryKey = false
    ) {
    }

    // ── Fluent modifiers (return $this for chaining) ──────────────────────

    public function nullable(bool $value = true): static
    {
        $this->nullable = $value;

        return $this;
    }

    public function unsigned(bool $value = true): static
    {
        $this->unsigned = $value;

        return $this;
    }

    public function unique(bool $value = true): static
    {
        $this->unique = $value;

        return $this;
    }

    public function default(mixed $value): static
    {
        $this->default = $value;

        return $this;
    }

    public function useCurrent(): static
    {
        $this->useCurrent = true;

        return $this;
    }

    /**
     * For enum() columns — the allowed values.
     *
     * @param  array<int, string>  $values
     */
    public function values(array $values): static
    {
        $this->values = $values;

        return $this;
    }

    /**
     * @return array{name:string,type:string,length:?int,unsigned:bool,nullable:bool,unique:bool,default:mixed,useCurrent:bool,values:array}
     */
    public function toArray(): array
    {
        return [
            'name'       => $this->name,
            'type'       => $this->type,
            'length'     => $this->length,
            'unsigned'   => $this->unsigned,
            'nullable'   => $this->nullable,
            'unique'     => $this->unique,
            'default'    => $this->default,
            'useCurrent' => $this->useCurrent,
            'values'     => $this->values,
            'primaryKey' => $this->primaryKey,
        ];
    }
}
