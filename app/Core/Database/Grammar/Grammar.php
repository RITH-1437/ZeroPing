<?php

namespace App\Core\Database\Grammar;

use App\Core\Database\Blueprint;

/**
 * Base SQL grammar.
 *
 * A grammar turns an abstract Blueprint (a list of column / table operations)
 * into engine-specific SQL. The Blueprint never emits final SQL itself; it
 * only describes intent. This keeps migrations identical across every
 * supported database.
 *
 * Concrete grammars override the few primitives that actually differ
 * (auto-increment syntax, column type names, current-timestamp, listing
 * tables, ...). Everything else is inherited.
 */
abstract class Grammar
{
    abstract public function wrap(string $name): string;

    abstract public function autoIncrementPrimary(): string;

    abstract public function typeMap(string $type, ?int $length = null): string;

    abstract public function currentTimestamp(): string;

    abstract public function listTablesSql(): string;

    abstract public function tableExistsSql(string $table): string;

    public function supportsType(string $type): bool
    {
        return true;
    }

    public function compileCreate(Blueprint $blueprint): string
    {
        $columns = [];

        foreach ($blueprint->getColumns() as $column) {
            $columns[] = '    ' . $this->compileColumn($column->toArray());
        }

        if ($blueprint->getPrimaryKey() !== null) {
            // Skip the table-level clause when the primary-key column is
            // already declared inline (e.g. `id INTEGER PRIMARY KEY
            // AUTOINCREMENT`), otherwise engines like SQLite reject the
            // duplicate constraint.
            $inline = false;
            foreach ($blueprint->getColumns() as $column) {
                if (
                    ($column->toArray()['name'] ?? null) === $blueprint->getPrimaryKey()
                    && !empty($column->toArray()['primaryKey'])
                ) {
                    $inline = true;
                    break;
                }
            }
            if (!$inline) {
                $columns[] = '    PRIMARY KEY (' . $this->wrap($blueprint->getPrimaryKey()) . ')';
            }
        }

        foreach ($blueprint->getCommands() as $command) {
            $sql = $this->compileCommand($command);

            if ($sql !== null) {
                $columns[] = '    ' . $sql;
            }
        }

        $table = $this->wrap($blueprint->getTable());

        return "CREATE TABLE IF NOT EXISTS {$table} (\n"
            . implode(",\n", $columns)
            . "\n)";
    }

    public function compileAdd(Blueprint $blueprint): array
    {
        $queries = [];

        foreach ($blueprint->getColumns() as $column) {
            $table = $this->wrap($blueprint->getTable());
            $queries[] = 'ALTER TABLE ' . $table . ' ADD COLUMN ' . $this->compileColumn($column->toArray());
        }

        return $queries;
    }

    public function compileDropColumn(Blueprint $blueprint, array $columns): string
    {
        $table = $this->wrap($blueprint->getTable());
        $dropped = array_map(fn ($c) => $this->wrap($c), $columns);

        return 'ALTER TABLE ' . $table . ' DROP COLUMN ' . implode(', ', $dropped);
    }

    public function compileDrop(string $table): string
    {
        return 'DROP TABLE IF EXISTS ' . $this->wrap($table);
    }

    protected function compileColumn(array $column): string
    {
        if (!empty($column['primaryKey'])) {
            return $this->wrap($column['name']) . ' ' . $this->autoIncrementPrimary();
        }

        $sql = $this->wrap($column['name']) . ' ' . $this->typeMap($column['type'], $column['length']);

        if ($column['type'] === 'enum' && !empty($column['values'])) {
            $quoted = array_map(
                fn ($v) => "'" . str_replace("'", "''", (string) $v) . "'",
                $column['values']
            );
            $sql .= ' CHECK (' . $this->wrap($column['name']) . ' IN (' . implode(', ', $quoted) . '))';
        }

        if ($column['unsigned'] && $this->supportsUnsigned()) {
            $sql .= ' UNSIGNED';
        }

        if ($column['unique']) {
            $sql .= ' UNIQUE';
        }

        $sql .= $column['nullable'] ? ' NULL' : ' NOT NULL';

        if ($column['useCurrent']) {
            $sql .= ' DEFAULT ' . $this->currentTimestamp();
        } elseif (array_key_exists('default', $column) && $column['default'] !== null) {
            $sql .= ' DEFAULT ' . $this->quoteDefault($column['default']);
        }

        return $sql;
    }

    protected function compileCommand(array $command): ?string
    {
        if ($command['name'] === 'foreign') {
            return $this->compileForeign($command);
        }

        return null;
    }

    protected function compileForeign(array $command): string
    {
        $column    = $this->wrap($command['column']);
        $refTable  = $this->wrap($command['referencesTable']);
        $refColumn = $this->wrap($command['referencesColumn']);
        $sql       = 'CONSTRAINT ' . $this->wrap($command['index'])
            . ' FOREIGN KEY (' . $column . ') REFERENCES ' . $refTable . ' (' . $refColumn . ')';

        if (isset($command['onDelete'])) {
            $sql .= ' ON DELETE ' . $command['onDelete'];
        }

        if (isset($command['onUpdate'])) {
            $sql .= ' ON UPDATE ' . $command['onUpdate'];
        }

        return $sql;
    }

    protected function supportsUnsigned(): bool
    {
        return true;
    }

    protected function quoteDefault($value): string
    {
        if (is_int($value) || is_float($value)) {
            return (string) $value;
        }

        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        return "'" . str_replace("'", "''", (string) $value) . "'";
    }

    public function compileMigrationTable(): string
    {
        $table = $this->wrap('migrations');
        $id    = $this->wrap('id') . ' ' . $this->autoIncrementPrimary();

        return "CREATE TABLE IF NOT EXISTS {$table} (\n"
            . '    ' . $id . ",\n"
            . '    ' . $this->wrap('migration') . " VARCHAR(255) NOT NULL,\n"
            . '    ' . $this->wrap('batch') . " INT NOT NULL,\n"
            . '    ' . $this->wrap('created_at') . ' ' . $this->timestampType()
            . ' DEFAULT ' . $this->currentTimestamp() . "\n"
            . ')';
    }

    protected function timestampType(): string
    {
        return 'TIMESTAMP';
    }

    protected function migrationIdColumn(): string
    {
        return $this->wrap('id') . ' ' . $this->autoIncrementPrimary();
    }

    public function compileInsertMigration(): string
    {
        return 'INSERT INTO ' . $this->wrap('migrations')
            . ' (' . $this->wrap('migration') . ', ' . $this->wrap('batch') . ")\n"
            . 'VALUES (?, ?)';
    }

    public function compileDeleteMigration(): string
    {
        return 'DELETE FROM ' . $this->wrap('migrations')
            . ' WHERE ' . $this->wrap('migration') . ' = ?';
    }

    public function selectExecutedMigrations(): string
    {
        return 'SELECT ' . $this->wrap('migration') . ' FROM ' . $this->wrap('migrations');
    }

    public function selectMaxBatch(): string
    {
        return 'SELECT MAX(' . $this->wrap('batch') . ') FROM ' . $this->wrap('migrations');
    }

    public function selectMigrationsInBatch(): string
    {
        return 'SELECT ' . $this->wrap('migration') . ' FROM ' . $this->wrap('migrations')
            . ' WHERE ' . $this->wrap('batch') . ' = ? ORDER BY ' . $this->wrap('id') . ' ASC';
    }

    public function disableForeignKeys(): string
    {
        return '';
    }

    public function enableForeignKeys(): string
    {
        return '';
    }
}
