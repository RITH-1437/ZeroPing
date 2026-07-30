<?php

declare(strict_types=1);

namespace App\Core\Validation\Rules;

/**
 * Validates that a field value is numeric.
 *
 * Uses PHP's is_numeric() which accepts integers, floats, and numeric strings.
 * Passes when the value is null (handled by "required" rule).
 */
class NumericRule extends AbstractRule
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
        if ($value === null) {
            return true;
        }

        return is_numeric($value);
    }

    /**
     * {@inheritDoc}
     */
    public function message(string $field, array $parameters = []): string
    {
        return "The {$this->formatFieldName($field)} field must be numeric.";
    }
}
