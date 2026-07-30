<?php

declare(strict_types=1);

namespace App\Core\Validation\Rules;

/**
 * Validates that a field value matches another field's value.
 *
 * The other field name is passed as the first parameter (e.g., "same:password").
 * Uses strict equality comparison.
 */
class SameRule extends AbstractRule
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
        $otherField = $this->parameter($parameters, 0);

        if ($otherField === null) {
            return false;
        }

        return ($data[$otherField] ?? null) === $value;
    }

    /**
     * {@inheritDoc}
     */
    public function message(string $field, array $parameters = []): string
    {
        $otherField = $this->parameter($parameters, 0, 'unknown');

        return "The {$this->formatFieldName($field)} field must match {$this->formatFieldName((string) $otherField)}.";
    }
}
