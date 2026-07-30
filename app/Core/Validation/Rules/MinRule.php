<?php

declare(strict_types=1);

namespace App\Core\Validation\Rules;

/**
 * Validates that a field value meets a minimum constraint.
 *
 * For numeric values, checks that the number is >= the minimum.
 * For strings, checks that the character length is >= the minimum.
 * For arrays, checks that the element count is >= the minimum.
 */
class MinRule extends AbstractRule
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

        $min = (int) $this->parameter($parameters, 0, 0);

        return $this->getSize($value) >= $min;
    }

    /**
     * {@inheritDoc}
     */
    public function message(string $field, array $parameters = []): string
    {
        $min = $this->parameter($parameters, 0, '0');

        return "The {$this->formatFieldName($field)} field must be at least {$min}.";
    }
}
