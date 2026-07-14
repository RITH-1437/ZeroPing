<?php

namespace App\Core\Database;

use App\Core\Database\Grammar\Grammar;

/**
 * A fluent schema blueprint.
 *
 * Blueprint records what a table should look like (columns, primary key,
 * foreign keys, drops). It does NOT know how to write SQL — that is the
 * Grammar's job. This is what lets a single migration run unchanged on
 * SQLite, MySQL, MariaDB and PostgreSQL.
 */
class Blueprint
{
    private string $table;

    /** @var ColumnDefinition[] */
    private array $columns = [];

    /** @var array<int, array{name:string, ...}> */
    private array $commands = [];

    /** @var string[] */
    private array $dropped = [];

    private ?string $primaryKey = null;

    private Grammar $grammar;

    public function __construct(string $table, ?Grammar $grammar = null)
    {
        $this->table = $table;
        $this->grammar = $grammar ?? (new \App\Core\Database\Grammar\MySqlGrammar());
    }

    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * @return ColumnDefinition[]
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * @return array<int, array{name:string, ...}>
     */
    public function getCommands(): array
    {
        return $this->commands;
    }

    /**
     * @return string[]
     */
    public function getDropped(): array
    {
        return $this->dropped;
    }

    public function getPrimaryKey(): ?string
    {
        return $this->primaryKey;
    }

    public function grammar(): Grammar
    {
        return $this->grammar;
    }

    // ── Column definitions ───────────────────────────────────────────────

    public function id(): static
    {
        $this->primaryKey = 'id';
        $this->columns[] = new ColumnDefinition('id', 'id', primaryKey: true);

        return $this;
    }

    public function string(string $name, int $length = 255): ColumnDefinition
    {
        return $this->addColumn('string', $name, $length);
    }

    public function char(string $name, int $length = 255): ColumnDefinition
    {
        return $this->addColumn('char', $name, $length);
    }

    public function text(string $name): ColumnDefinition
    {
        return $this->addColumn('text', $name);
    }

    public function longText(string $name): ColumnDefinition
    {
        return $this->addColumn('longText', $name);
    }

    public function mediumText(string $name): ColumnDefinition
    {
        return $this->addColumn('mediumText', $name);
    }

    public function integer(string $name): ColumnDefinition
    {
        return $this->addColumn('integer', $name);
    }

    public function bigInteger(string $name): ColumnDefinition
    {
        return $this->addColumn('bigInteger', $name);
    }

    public function tinyInteger(string $name): ColumnDefinition
    {
        return $this->addColumn('tinyInteger', $name);
    }

    public function unsignedInteger(string $name): ColumnDefinition
    {
        return $this->addColumn('unsignedInteger', $name);
    }

    public function unsignedBigInteger(string $name): ColumnDefinition
    {
        return $this->addColumn('unsignedBigInteger', $name);
    }

    public function unsignedTinyInteger(string $name): ColumnDefinition
    {
        return $this->addColumn('unsignedTinyInteger', $name);
    }

    public function boolean(string $name): ColumnDefinition
    {
        return $this->addColumn('boolean', $name);
    }

    public function decimal(string $name, int $precision = 8, int $scale = 2): ColumnDefinition
    {
        return $this->addColumn('decimal', $name, (int) ($precision . $scale));
    }

    public function float(string $name): ColumnDefinition
    {
        return $this->addColumn('float', $name);
    }

    public function double(string $name): ColumnDefinition
    {
        return $this->addColumn('double', $name);
    }

    public function date(string $name): ColumnDefinition
    {
        return $this->addColumn('date', $name);
    }

    public function dateTime(string $name): ColumnDefinition
    {
        return $this->addColumn('dateTime', $name);
    }

    public function timestamp(string $name): ColumnDefinition
    {
        return $this->addColumn('timestamp', $name);
    }

    public function json(string $name): ColumnDefinition
    {
        return $this->addColumn('json', $name);
    }

    /**
     * @param  array<int, string>  $values
     */
    public function enum(string $name, array $values): ColumnDefinition
    {
        $column = $this->addColumn('enum', $name);
        $column->values($values);

        return $column;
    }

    public function timestamps(): static
    {
        $this->columns[] = new ColumnDefinition('created_at', 'timestamp', useCurrent: true);
        $this->columns[] = new ColumnDefinition('updated_at', 'timestamp', nullable: true);

        return $this;
    }

    public function softDeletes(): static
    {
        $this->columns[] = new ColumnDefinition('deleted_at', 'timestamp', nullable: true);

        return $this;
    }

    public function foreign(string $name): ForeignDefinition
    {
        $this->addColumn('unsignedBigInteger', $name);

        $this->commands[] = [
            'name'             => 'foreign',
            'table'            => $this->table,
            'column'           => $name,
            'referencesTable'  => null,
            'referencesColumn' => null,
            'index'            => $this->table . '_' . $name . '_foreign',
        ];

        return new ForeignDefinition($this, $name);
    }

    /**
     * @internal used by ForeignDefinition to record command attributes.
     */
    public function addForeignCommand(string $column, string $key, mixed $value): void
    {
        $reversed = array_reverse($this->commands, true);

        foreach ($reversed as $index => $command) {
            if (($command['name'] ?? null) === 'foreign' && ($command['column'] ?? null) === $column) {
                $this->commands[$index][$key] = $value;

                return;
            }
        }
    }

    public function dropColumn(string ...$names): static
    {
        foreach ($names as $name) {
            $this->dropped[] = $name;
        }

        return $this;
    }

    // ── Compilation (delegated to the Grammar) ─────────────────────────

    /**
     * @return string[]
     */
    public function toSql(): array
    {
        return [$this->grammar->compileCreate($this)];
    }

    /**
     * @return string[]
     */
    public function toAlterSql(): array
    {
        $queries = [];

        if (!empty($this->columns)) {
            $queries = array_merge($queries, $this->grammar->compileAdd($this));
        }

        if (!empty($this->dropped)) {
            $queries[] = $this->grammar->compileDropColumn($this, $this->dropped);
        }

        return $queries;
    }

    // ── Helpers ─────────────────────────────────────────────────────────

    private function addColumn(string $type, string $name, ?int $length = null): ColumnDefinition
    {
        $column = new ColumnDefinition($name, $type, $length);
        $this->columns[] = $column;

        return $column;
    }
}
