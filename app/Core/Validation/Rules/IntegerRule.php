<?php

declare(strict_types=1);

namespace App\Core\Validation\Rules;

/**
 * Validates that a field value is an integer.
 *
 * Uses PHP's FILTER_VALIDATE_INT filter, which accepts integer strings.
 * Passes when the value is null (handled by "required" rule).
 */
class IntegerRule extends AbstractRule
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

        return filter_var($value, FILTER_VALIDATE_INT) !== false;
    }

    /**
     * {@inheritDoc}
     */
    public function message(string $field, array $parameters = []): string
    {
        return "The {$this->formatFieldName($field)} field must be an integer.";
    }
}
