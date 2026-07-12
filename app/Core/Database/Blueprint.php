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

class Blueprint
{
    protected string $table;

    /** @var array<string|ColumnDefinition> */
    protected array $columns = [];

    /** @var array<string> */
    protected array $dropped = [];

    public function __construct(string $table)
    {
        $this->table = $table;
    }

    public function id(): static
    {
        $this->columns[] = "id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY";
        return $this;
    }

    public function string(string $name, int $length = 255): ColumnDefinition
    {
        $col = new ColumnDefinition("{$name} VARCHAR({$length})");
        $this->columns[] = $col;
        return $col;
    }

    public function text(string $name): ColumnDefinition
    {
        $col = new ColumnDefinition("{$name} TEXT");
        $this->columns[] = $col;
        return $col;
    }

    public function longText(string $name): ColumnDefinition
    {
        $col = new ColumnDefinition("{$name} LONGTEXT");
        $this->columns[] = $col;
        return $col;
    }

    public function mediumText(string $name): ColumnDefinition
    {
        $col = new ColumnDefinition("{$name} MEDIUMTEXT");
        $this->columns[] = $col;
        return $col;
    }

    public function integer(string $name): ColumnDefinition
    {
        $col = new ColumnDefinition("{$name} INT");
        $this->columns[] = $col;
        return $col;
    }

    public function unsignedInteger(string $name): ColumnDefinition
    {
        $col = new ColumnDefinition("{$name} INT UNSIGNED");
        $this->columns[] = $col;
        return $col;
    }

    public function unsignedBigInteger(string $name): ColumnDefinition
    {
        $col = new ColumnDefinition("{$name} BIGINT UNSIGNED");
        $this->columns[] = $col;
        return $col;
    }

    public function unsignedTinyInteger(string $name): ColumnDefinition
    {
        $col = new ColumnDefinition("{$name} TINYINT UNSIGNED");
        $this->columns[] = $col;
        return $col;
    }

    public function bigInteger(string $name): ColumnDefinition
    {
        $col = new ColumnDefinition("{$name} BIGINT");
        $this->columns[] = $col;
        return $col;
    }

    public function tinyInteger(string $name): ColumnDefinition
    {
        $col = new ColumnDefinition("{$name} TINYINT");
        $this->columns[] = $col;
        return $col;
    }

    public function boolean(string $name): ColumnDefinition
    {
        $col = new ColumnDefinition("{$name} TINYINT(1)");
        $this->columns[] = $col;
        return $col;
    }

    public function decimal(string $name, int $precision = 8, int $scale = 2): ColumnDefinition
    {
        $col = new ColumnDefinition("{$name} DECIMAL({$precision},{$scale})");
        $this->columns[] = $col;
        return $col;
    }

    public function float(string $name): ColumnDefinition
    {
        $col = new ColumnDefinition("{$name} FLOAT");
        $this->columns[] = $col;
        return $col;
    }

    public function double(string $name): ColumnDefinition
    {
        $col = new ColumnDefinition("{$name} DOUBLE");
        $this->columns[] = $col;
        return $col;
    }

    public function date(string $name): ColumnDefinition
    {
        $col = new ColumnDefinition("{$name} DATE");
        $this->columns[] = $col;
        return $col;
    }

    public function dateTime(string $name): ColumnDefinition
    {
        $col = new ColumnDefinition("{$name} DATETIME");
        $this->columns[] = $col;
        return $col;
    }

    public function timestamp(string $name): ColumnDefinition
    {
        $col = new ColumnDefinition("{$name} TIMESTAMP");
        $this->columns[] = $col;
        return $col;
    }

    public function json(string $name): ColumnDefinition
    {
        $col = new ColumnDefinition("{$name} JSON");
        $this->columns[] = $col;
        return $col;
    }

    public function enum(string $name, array $values): ColumnDefinition
    {
        $list = implode("','", $values);
        $col = new ColumnDefinition("{$name} ENUM('{$list}')");
        $this->columns[] = $col;
        return $col;
    }

    public function timestamps(): static
    {
        $this->columns[] = "created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP";
        $this->columns[] = "updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP";
        return $this;
    }

    public function softDeletes(): static
    {
        $this->columns[] = "deleted_at TIMESTAMP NULL DEFAULT NULL";
        return $this;
    }

    public function foreign(string $name): ColumnDefinition
    {
        $col = new ColumnDefinition("{$name} BIGINT UNSIGNED");
        $this->columns[] = $col;
        return $col;
    }

    public function dropColumn(string ...$names): static
    {
        foreach ($names as $name) {
            $this->dropped[] = $name;
        }

        return $this;
    }

    public function build(): string
    {
        $parts = [];
        foreach ($this->columns as $col) {
            $parts[] = $col instanceof ColumnDefinition ? $col->toSql() : $col;
        }
        return implode(",\n    ", $parts);
    }

    public function toSql(): array
    {
        return [
            "CREATE TABLE IF NOT EXISTS `{$this->table}` (\n    " . $this->build() . "\n)"
        ];
    }

    public function toAlterSql(): array
    {
        $queries = [];

        if (!empty($this->columns)) {
            $parts = [];
            foreach ($this->columns as $col) {
                $parts[] = 'ADD COLUMN ' . ($col instanceof ColumnDefinition ? $col->toSql() : $col);
            }
            $queries[] = "ALTER TABLE `{$this->table}`\n    " . implode(",\n    ", $parts);
        }

        if (!empty($this->dropped)) {
            $cols = array_map(fn(string $name): string => "`{$name}`", $this->dropped);
            $queries[] = "ALTER TABLE `{$this->table}` DROP COLUMN " . implode(', ', $cols);
        }

        return $queries;
    }
}
