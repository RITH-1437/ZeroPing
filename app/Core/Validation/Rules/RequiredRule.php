<?php

declare(strict_types=1);

namespace App\Core\Validation\Rules;

/**
 * Validates that a field is present and not empty.
 *
 * A field is considered "present" if it is not null, not an empty string,
 * and not an empty array.
 */
class RequiredRule extends AbstractRule
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
        return !$this->isEmpty($value);
    }

    /**
     * {@inheritDoc}
     */
    public function message(string $field, array $parameters = []): string
    {
        return "The {$this->formatFieldName($field)} field is required.";
    }
}
