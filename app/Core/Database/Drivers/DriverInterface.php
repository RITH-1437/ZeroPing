<?php

namespace App\Core\Database\Drivers;

use PDO;

/**
 * Contract every database driver must satisfy.
 *
 * A driver knows how to turn a connection configuration array into a live
 * PDO instance and how to render engine-specific SQL through its Grammar.
 * Adding SQL Server or Oracle later means writing one more class that
 * implements this interface — the ORM, QueryBuilder and Model never change.
 */
interface DriverInterface
{
    /**
     * Human-readable driver name (sqlite, mysql, mariadb, pgsql, ...).
     */
    public function getName(): string;

    /**
     * The PHP PDO extension required to use this driver.
     */
    public function getPdoExtension(): string;

    /**
     * Build a configured PDO connection from the given config.
     *
     * @param  array<string, mixed>  $config
     */
    public function connect(array $config): PDO;

    /**
     * The Grammar that compiles Blueprint definitions for this engine.
     */
    public function grammar(): \App\Core\Database\Grammar\Grammar;

    /**
     * Quote (escape) a single identifier — table or column name.
     */
    public function quoteIdentifier(string $name): string;

    /**
     * Whether the underlying engine supports the given feature.
     */
    public function supports(string $feature): bool;
}
