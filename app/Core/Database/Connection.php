<?php

namespace App\Core\Database;

use App\Core\Database\Drivers\DriverInterface;
use App\Core\Database\Grammar\Grammar;
use PDO;

/**
 * A live, named database connection.
 *
 * Wraps the PDO instance together with the driver that built it and the
 * grammar used to compile SQL for this engine. Application code (models,
 * query builder, migrations) only ever talks to this object or the
 * DatabaseManager — never to a raw driver or DSN.
 */
class Connection
{
    private PDO $pdo;

    public function __construct(
        private DriverInterface $driver,
        ?PDO $pdo = null,
        private ?string $name = null
    ) {
        $this->name = $name ?? $driver->getName();
        $this->pdo = $pdo ?? $driver->connect([]);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function driver(): DriverInterface
    {
        return $this->driver;
    }

    public function grammar(): Grammar
    {
        return $this->driver->grammar();
    }

    public function pdo(): PDO
    {
        return $this->pdo;
    }

    /**
     * Run a SELECT and return every row.
     */
    public function select(string $sql, array $bindings = []): array
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($bindings);

        return $stmt->fetchAll();
    }

    public function statement(string $sql, array $bindings = []): bool
    {
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute($bindings);
    }

    public function affectingStatement(string $sql, array $bindings = []): int
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($bindings);

        return $stmt->rowCount();
    }

    public function beginTransaction(): void
    {
        $this->pdo->beginTransaction();
    }

    public function commit(): void
    {
        $this->pdo->commit();
    }

    public function rollBack(): void
    {
        $this->pdo->rollBack();
    }

    public function inTransaction(): bool
    {
        return $this->pdo->inTransaction();
    }

    /**
     * Quote an identifier for this engine.
     */
    public function wrap(string $name): string
    {
        return $this->driver->quoteIdentifier($name);
    }

    public function hasTable(string $table): bool
    {
        $rows = $this->select($this->grammar()->tableExistsSql($table));

        return count($rows) > 0;
    }

    /**
     * Return every user table name in the current database.
     *
     * @return string[]
     */
    public function listTables(): array
    {
        $rows = $this->select($this->grammar()->listTablesSql());

        return array_map(static fn ($row) => array_values($row)[0], $rows);
    }
}
