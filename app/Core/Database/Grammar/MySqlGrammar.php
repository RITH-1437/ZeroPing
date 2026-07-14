<?php

namespace App\Core\Database\Grammar;

/**
 * MySQL grammar.
 */
class MySqlGrammar extends Grammar
{
    public function wrap(string $name): string
    {
        return '`' . str_replace('`', '``', $name) . '`';
    }

    public function autoIncrementPrimary(): string
    {
        return 'BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY';
    }

    public function typeMap(string $type, ?int $length = null): string
    {
        return match ($type) {
            'id'                             => 'BIGINT UNSIGNED',
            'string'                         => 'VARCHAR(' . ($length ?? 255) . ')',
            'char'                           => 'CHAR(' . ($length ?? 255) . ')',
            'text'                           => 'TEXT',
            'mediumText'                     => 'MEDIUMTEXT',
            'longText'                       => 'LONGTEXT',
            'json'                           => 'JSON',
            'integer'                        => 'INT',
            'bigInteger'                     => 'BIGINT',
            'tinyInteger'                    => 'TINYINT',
            'unsignedInteger'                => 'INT UNSIGNED',
            'unsignedBigInteger'             => 'BIGINT UNSIGNED',
            'unsignedTinyInteger'            => 'TINYINT UNSIGNED',
            'boolean'                        => 'TINYINT(1)',
            'decimal'                        => 'DECIMAL(' . ($length ?? '8,2') . ')',
            'float'                          => 'FLOAT',
            'double'                         => 'DOUBLE',
            'date'                           => 'DATE',
            'dateTime'                       => 'DATETIME',
            'timestamp'                      => 'TIMESTAMP',
            'enum'                           => 'ENUM',
            default                          => 'VARCHAR(255)',
        };
    }

    public function currentTimestamp(): string
    {
        return 'CURRENT_TIMESTAMP';
    }

    public function listTablesSql(): string
    {
        return 'SHOW TABLES';
    }

    public function tableExistsSql(string $table): string
    {
        return 'SHOW TABLES LIKE ' . $this->quoteDefault($table);
    }

    public function disableForeignKeys(): string
    {
        return 'SET FOREIGN_KEY_CHECKS = 0';
    }

    public function enableForeignKeys(): string
    {
        return 'SET FOREIGN_KEY_CHECKS = 1';
    }
}
