<?php

declare(strict_types=1);

namespace App\Core\Validation\Rules;

/**
 * Validates that a field value falls between a minimum and maximum.
 *
 * For numeric values, checks the numeric range.
 * For strings, checks the character length range.
 * For arrays, checks the element count range.
 */
class BetweenRule extends AbstractRule
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
        $max = (int) $this->parameter($parameters, 1, PHP_INT_MAX);

        $size = $this->getSize($value);

        return $size >= $min && $size <= $max;
    }

    /**
     * {@inheritDoc}
     */
    public function message(string $field, array $parameters = []): string
    {
        $min = $this->parameter($parameters, 0, '0');
        $max = $this->parameter($parameters, 1, '0');

        return "The {$this->formatFieldName($field)} field must be between {$min} and {$max}.";
    }
}
