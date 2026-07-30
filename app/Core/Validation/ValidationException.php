<?php

declare(strict_types=1);

namespace App\Core\Validation;

use Exception;

/**
 * Exception thrown when validation fails.
 *
 * Carries the validation errors so they can be caught and
 * transformed into an appropriate HTTP response.
 */
class ValidationException extends Exception
{
    /**
     * The validation errors.
     *
     * @var array<string, string[]>
     */
    protected array $errors;

    /**
     * Create a new validation exception.
     *
     * @param array<string, string[]> $errors The validation errors keyed by field name.
     */
    public function __construct(array $errors)
    {
        $this->errors = $errors;

        $message = 'Validation failed for fields: ' . implode(', ', array_keys($errors));

        parent::__construct($message);
    }

    /**
     * Get the validation errors.
     *
     * @return array<string, string[]> The validation errors keyed by field name.
     */
    public function errors(): array
    {
        return $this->errors;
    }

    /**
     * Get the first error message across all fields.
     *
     * @return string|null The first error message, or null if empty.
     */
    public function firstError(): ?string
    {
        foreach ($this->errors as $fieldErrors) {
            if (!empty($fieldErrors)) {
                return $fieldErrors[0];
            }
        }

        return null;
    }
}
