<?php

declare(strict_types=1);

namespace App\Core\Validation\Rules;

use App\Core\Application\App;
use App\Core\Validation\DatabasePresenceVerifier;

/**
 * Validates that a field value is unique in a database table.
 *
 * Parameters: table[,column[,except_id[,id_column]]]
 * - table: The database table to check against.
 * - column: The column to check (defaults to the field name).
 * - except_id: An ID to exclude from the check (for updates).
 * - id_column: The primary key column name (defaults to "id").
 *
 * @example "unique:users,email" — email must be unique in users table
 * @example "unique:users,email,5,id" — unique except for record with id=5
 */
class UniqueRule extends AbstractRule
{
    /**
     * {@inheritDoc}
     */
    public function validate(
        string $field,
        mixed $value,
        array $data = [],
        array $parameters = []
    ): bool {
        if ($this->isEmpty($value)) {
            return true;
        }

        $table = $this->parameter($parameters, 0);
        $column = $this->parameter($parameters, 1, $field);
        $exceptId = $this->parameter($parameters, 2);
        $idColumn = $this->parameter($parameters, 3, 'id');

        if ($table === null) {
            return false;
        }

        /** @var DatabasePresenceVerifier $verifier */
        $verifier = App::container()->make(DatabasePresenceVerifier::class);

        return $verifier->unique(
            (string) $table,
            (string) $column,
            $value,
            $exceptId,
            (string) $idColumn
        );
    }

    /**
     * {@inheritDoc}
     */
    public function message(string $field, array $parameters = []): string
    {
        return "The {$this->formatFieldName($field)} has already been taken.";
    }
}
