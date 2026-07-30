<?php

declare(strict_types=1);

namespace App\Core\Validation\Rules;

/**
 * Validates that a field value is one of a set of allowed values.
 *
 * The allowed values are passed as rule parameters (e.g., "in:admin,user,guest").
 * Comparison is done as a strict string check.
 */
class InRule extends AbstractRule
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

        return in_array((string) $value, $parameters, true);
    }

    /**
     * {@inheritDoc}
     */
    public function message(string $field, array $parameters = []): string
    {
        $allowed = implode(', ', $parameters);

        return "The {$this->formatFieldName($field)} field must be one of: {$allowed}.";
    }
}
