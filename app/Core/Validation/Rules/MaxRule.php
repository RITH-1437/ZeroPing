<?php

declare(strict_types=1);

namespace App\Core\Validation\Rules;

/**
 * Validates that a field value does not exceed a maximum constraint.
 *
 * For numeric values, checks that the number is <= the maximum.
 * For strings, checks that the character length is <= the maximum.
 * For arrays, checks that the element count is <= the maximum.
 */
class MaxRule extends AbstractRule
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

        $max = (int) $this->parameter($parameters, 0, PHP_INT_MAX);

        return $this->getSize($value) <= $max;
    }

    /**
     * {@inheritDoc}
     */
    public function message(string $field, array $parameters = []): string
    {
        $max = $this->parameter($parameters, 0, '0');

        return "The {$this->formatFieldName($field)} field must not exceed {$max}.";
    }
}
