<?php

declare(strict_types=1);

namespace App\Core\Validation\Rules;

use App\Core\Application\App;
use App\Core\Validation\DatabasePresenceVerifier;

/**
 * Validates that a field value exists in a database table.
 *
 * Parameters: table[,column]
 * - table: The database table to check against.
 * - column: The column to check (defaults to the field name).
 *
 * @example "exists:users,id" — value must exist in users.id column
 */
class ExistsRule extends AbstractRule
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

        if ($table === null) {
            return false;
        }

        /** @var DatabasePresenceVerifier $verifier */
        $verifier = App::container()->make(DatabasePresenceVerifier::class);

        return $verifier->exists(
            (string) $table,
            (string) $column,
            $value
        );
    }

    /**
     * {@inheritDoc}
     */
    public function message(string $field, array $parameters = []): string
    {
        return "The selected {$this->formatFieldName($field)} is invalid.";
    }
}
