<?php

namespace App\Core\Database\Grammar;

/**
 * SQLite grammar.
 *
 * SQLite has a very small type system (type affinity) so most logical
 * column types collapse to TEXT / INTEGER / REAL / NUMERIC. Auto-increment
 * primary keys must be declared as "INTEGER PRIMARY KEY AUTOINCREMENT".
 */
class SQLiteGrammar extends Grammar
{
    public function wrap(string $name): string
    {
        return '"' . str_replace('"', '""', $name) . '"';
    }

    public function autoIncrementPrimary(): string
    {
        return 'INTEGER PRIMARY KEY AUTOINCREMENT';
    }

    public function typeMap(string $type, ?int $length = null): string
    {
        return match ($type) {
            'id'                          => 'INTEGER',
            'string', 'char', 'varchar'   => 'TEXT',
            'text', 'longText', 'mediumText', 'json' => 'TEXT',
            'integer', 'bigInteger', 'tinyInteger',
            'unsignedInteger', 'unsignedBigInteger',
            'unsignedTinyInteger', 'boolean' => 'INTEGER',
            'decimal', 'float', 'double'   => 'REAL',
            'date', 'dateTime', 'timestamp' => 'TEXT',
            'enum'                         => 'TEXT',
            default                        => 'TEXT',
        };
    }

    public function currentTimestamp(): string
    {
        return 'CURRENT_TIMESTAMP';
    }

    public function listTablesSql(): string
    {
        return "SELECT name FROM sqlite_master WHERE type = 'table' AND name NOT LIKE 'sqlite_%'";
    }

    public function tableExistsSql(string $table): string
    {
        return "SELECT name FROM sqlite_master WHERE type = 'table' AND name = " . $this->quoteDefault($table);
    }

    protected function supportsUnsigned(): bool
    {
        // SQLite ignores UNSIGNED; affinity already handles the value range.
        return false;
    }

    protected function compileCommand(array $command): ?string
    {
        if ($command['name'] === 'foreign') {
            // SQLite only enforces foreign keys inside the CREATE TABLE body.
            return $this->compileForeign($command);
        }

        return null;
    }
}
