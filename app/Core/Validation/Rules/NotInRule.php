<?php

declare(strict_types=1);

namespace App\Core\Validation\Rules;

/**
 * Validates that a field value is NOT one of a set of disallowed values.
 *
 * The disallowed values are passed as rule parameters (e.g., "not_in:banned,blocked").
 * Comparison is done as a strict string check.
 */
class NotInRule extends AbstractRule
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

        return !in_array((string) $value, $parameters, true);
    }

    /**
     * {@inheritDoc}
     */
    public function message(string $field, array $parameters = []): string
    {
        $disallowed = implode(', ', $parameters);

        return "The {$this->formatFieldName($field)} field must not be one of: {$disallowed}.";
    }
}
