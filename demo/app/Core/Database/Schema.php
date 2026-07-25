<?php

namespace App\Core\Database;

use Closure;

/**
 * Fluent interface for defining and modifying database tables.
 *
 * Every operation is routing through the DatabaseManager, so it works on any
 * configured driver. When you obtain an instance via `Schema::connection($name)`
 * or `Schema::connection($connection)` every following call (create / table /
 * drop / hasTable) uses that same connection — never a different one.
 */
class Schema
{
    private ?string $connectionName;

    /**
     * The concrete Connection to use. When set, static helpers resolve to it
     * instead of (re)resolving a name, so a connection obtained from
     * `Schema::connection()` keeps operating on the same database.
     */
    private ?Connection $instance = null;

    public function __construct(
        private Connection $connection,
        ?string $connectionName = null
    ) {
        $this->connectionName = $connectionName;

        // When built from a concrete Connection (rather than a name) we
        // remember it so subsequent static calls stay on the same database.
        if ($connectionName === null) {
            self::$current = $this;
        }
    }

    public static function connection(Connection|string|null $name = null): self
    {
        if ($name instanceof Connection) {
            return new self($name);
        }

        self::$active = $name;

        return new self(Database::connection($name), $name);
    }

    /**
     * Create a new table.
     */
    public static function create(string $table, Closure $callback): void
    {
        $self = self::resolve();
        $grammar = $self->connection->grammar();

        $blueprint = new Blueprint($table, $grammar);
        $callback($blueprint);

        foreach ($blueprint->toSql() as $query) {
            $self->connection->statement($query);
        }
    }

    /**
     * Alter an existing table (add / drop columns, foreign keys, …).
     */
    public static function table(string $table, Closure $callback): void
    {
        $self = self::resolve();
        $grammar = $self->connection->grammar();

        $blueprint = new Blueprint($table, $grammar);
        $callback($blueprint);

        foreach ($blueprint->toAlterSql() as $query) {
            $self->connection->statement($query);
        }
    }

    /**
     * Drop a table if it exists.
     */
    public static function drop(string $table): void
    {
        $self = self::resolve();
        $query = $self->connection->grammar()->compileDrop($table);

        $self->connection->statement($query);
    }

    public function hasTable(string $table): bool
    {
        return $this->connection->hasTable($table);
    }

    /**
     * @internal exposed so the MigrationRunner can reuse the live connection.
     */
    public function getConnection(): Connection
    {
        return $this->connection;
    }

    /**
     * Drop a table only if it exists (alias of drop() which is safe).
     */
    public static function dropIfExists(string $table): void
    {
        self::drop($table);
    }

    /**
     * Resolve the Schema instance for a static call, honouring any
     * connection previously selected through `Schema::connection($name)` or a
     * Schema instance obtained via `Schema::connection($connection)`.
     */
    private static function resolve(): self
    {
        if (self::$current !== null) {
            return self::$current;
        }

        return new self(Database::connection(self::$active), self::$active);
    }

    /**
     * The instance to use for the next static call, when one was obtained
     * via `Schema::connection($connection)`. Null falls back to the
     * connection selected by name (or the default).
     */
    private static ?self $current = null;

    /**
     * The connection name to use for the next static call, if one was
     * explicitly selected. Null falls back to the default connection.
     */
    private static ?string $active = null;
}
