<?php

declare(strict_types=1);

namespace App\Core\Validation;

use App\Core\Database\Identifier;
use PDO;

/**
 * Verifies the presence or absence of values in the database.
 *
 * Used by the "exists" and "unique" validation rules to perform
 * database lookups. Receives its PDO connection via constructor injection
 * for proper dependency inversion and testability.
 */
class DatabasePresenceVerifier
{
    /**
     * The PDO database connection.
     */
    protected PDO $pdo;

    /**
     * Create a new DatabasePresenceVerifier instance.
     *
     * @param PDO $pdo The PDO connection instance, injected via the DI container.
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Determine whether a value exists in the given table and column.
     *
     * @param string $table  The database table name.
     * @param string $column The column to search in.
     * @param mixed  $value  The value to search for.
     *
     * @return bool True if at least one matching row exists.
     */
    public function exists(string $table, string $column, mixed $value): bool
    {
        return $this->countMatchingRows($table, $column, $value) > 0;
    }

    /**
     * Determine whether a value is unique in the given table and column.
     *
     * @param string      $table    The database table name.
     * @param string      $column   The column to search in.
     * @param mixed       $value    The value to check uniqueness for.
     * @param mixed|null  $exceptId An ID value to exclude from the check (for updates).
     * @param string      $idColumn The column name of the primary key (defaults to "id").
     *
     * @return bool True if the value does not exist (is unique), or only exists for the excluded ID.
     */
    public function unique(
        string $table,
        string $column,
        mixed $value,
        mixed $exceptId = null,
        string $idColumn = 'id'
    ): bool {
        if ($exceptId !== null) {
            return $this->countMatchingRowsExcept($table, $column, $value, $exceptId, $idColumn) === 0;
        }

        return $this->countMatchingRows($table, $column, $value) === 0;
    }

    /**
     * Count matching rows in the database for the given criteria.
     *
     * @param string $table  The database table name.
     * @param string $column The column to search in.
     * @param mixed  $value  The value to match against.
     *
     * @return int The number of matching rows.
     */
    protected function countMatchingRows(string $table, string $column, mixed $value): int
    {
        $table = Identifier::table($table);
        $column = Identifier::column($column);

        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM {$table} WHERE {$column} = ?"
        );

        $stmt->execute([$value]);

        return (int) $stmt->fetchColumn();
    }

    /**
     * Count matching rows excluding a specific record by ID.
     *
     * Used for unique validation on updates where the current record
     * should be excluded from the uniqueness check.
     *
     * @param string $table    The database table name.
     * @param string $column   The column to search in.
     * @param mixed  $value    The value to match against.
     * @param mixed  $exceptId The ID value to exclude.
     * @param string $idColumn The ID column name.
     *
     * @return int The number of matching rows (excluding the specified record).
     */
    protected function countMatchingRowsExcept(
        string $table,
        string $column,
        mixed $value,
        mixed $exceptId,
        string $idColumn
    ): int {
        $table = Identifier::table($table);
        $column = Identifier::column($column);
        $idColumn = Identifier::column($idColumn);

        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM {$table} WHERE {$column} = ? AND {$idColumn} != ?"
        );

        $stmt->execute([$value, $exceptId]);

        return (int) $stmt->fetchColumn();
    }
}
