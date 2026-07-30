<?php

declare(strict_types=1);

namespace App\Core\Validation\Rules;

/**
 * Validates that a field has a matching confirmation field.
 *
 * Looks for a "{field}_confirmation" key in the data array and
 * checks that its value matches the field being validated.
 * Uses strict equality comparison.
 */
class ConfirmedRule extends AbstractRule
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
        $confirmationField = $field . '_confirmation';

        return ($data[$confirmationField] ?? null) === $value;
    }

    /**
     * {@inheritDoc}
     */
    public function message(string $field, array $parameters = []): string
    {
        return "The {$this->formatFieldName($field)} confirmation does not match.";
    }
}
