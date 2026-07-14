<?php

namespace App\Core\Database\Grammar;

/**
 * PostgreSQL grammar.
 *
 * PostgreSQL has no AUTO_INCREMENT; surrogate keys use SERIAL (or IDENTITY).
 * We emit BIGSERIAL so the behaviour matches MySQL's auto-increment id().
 */
class PostgreSqlGrammar extends Grammar
{
    public function wrap(string $name): string
    {
        return '"' . str_replace('"', '""', $name) . '"';
    }

    public function autoIncrementPrimary(): string
    {
        return 'BIGSERIAL PRIMARY KEY';
    }

    public function typeMap(string $type, ?int $length = null): string
    {
        return match ($type) {
            'id'                             => 'BIGINT',
            'string'                         => 'VARCHAR(' . ($length ?? 255) . ')',
            'char'                           => 'CHAR(' . ($length ?? 255) . ')',
            'text'                           => 'TEXT',
            'mediumText'                     => 'TEXT',
            'longText'                       => 'TEXT',
            'json'                           => 'JSONB',
            'integer'                        => 'INTEGER',
            'bigInteger'                     => 'BIGINT',
            'tinyInteger'                    => 'SMALLINT',
            'unsignedInteger'                => 'INTEGER',
            'unsignedBigInteger'             => 'BIGINT',
            'unsignedTinyInteger'            => 'SMALLINT',
            'boolean'                        => 'BOOLEAN',
            'decimal'                        => 'DECIMAL(' . ($length ?? '8,2') . ')',
            'float'                          => 'REAL',
            'double'                         => 'DOUBLE PRECISION',
            'date'                           => 'DATE',
            'dateTime'                       => 'TIMESTAMP',
            'timestamp'                      => 'TIMESTAMP',
            'enum'                           => 'TEXT',
            default                          => 'VARCHAR(255)',
        };
    }

    public function currentTimestamp(): string
    {
        return 'CURRENT_TIMESTAMP';
    }

    public function listTablesSql(): string
    {
        return "SELECT table_name FROM information_schema.tables "
            . "WHERE table_schema = current_schema() AND table_type = 'BASE TABLE'";
    }

    public function tableExistsSql(string $table): string
    {
        return "SELECT table_name FROM information_schema.tables "
            . "WHERE table_schema = current_schema() AND table_name = "
            . $this->quoteDefault($table);
    }
}
