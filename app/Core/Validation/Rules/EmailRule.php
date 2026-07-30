<?php

declare(strict_types=1);

namespace App\Core\Validation\Rules;

/**
 * Validates that a field value is a valid email address.
 *
 * Uses PHP's built-in FILTER_VALIDATE_EMAIL filter.
 * Passes when the value is null or empty (handled by "required" rule).
 */
class EmailRule extends AbstractRule
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

        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * {@inheritDoc}
     */
    public function message(string $field, array $parameters = []): string
    {
        return "The {$this->formatFieldName($field)} field must be a valid email address.";
    }
}
