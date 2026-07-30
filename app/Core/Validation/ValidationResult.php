<?php

declare(strict_types=1);

namespace App\Core\Validation;

/**
 * Holds the results of a validation pass.
 *
 * Collects error messages keyed by field name and provides
 * methods to query the overall validation outcome.
 */
class ValidationResult
{
    /**
     * The accumulated validation errors.
     *
     * @var array<string, string[]>
     */
    protected array $errors = [];

    /**
     * Add a validation error for the given field.
     *
     * @param string $field   The field that failed validation.
     * @param string $message The error message describing the failure.
     *
     * @return void
     */
    public function add(string $field, string $message): void
    {
        $this->errors[$field][] = $message;
    }

    /**
     * Get all validation errors.
     *
     * @return array<string, string[]> An associative array of field names to error message arrays.
     */
    public function errors(): array
    {
        return $this->errors;
    }

    /**
     * Get the first error message for a specific field.
     *
     * @param string $field The field name to retrieve the error for.
     *
     * @return string|null The first error message, or null if no errors exist for the field.
     */
    public function first(string $field): ?string
    {
        return $this->errors[$field][0] ?? null;
    }

    /**
     * Determine if validation passed (no errors).
     *
     * @return bool True if there are no validation errors.
     */
    public function passes(): bool
    {
        return empty($this->errors);
    }

    /**
     * Determine if validation failed (has errors).
     *
     * @return bool True if there are validation errors.
     */
    public function fails(): bool
    {
        return !$this->passes();
    }

    /**
     * Determine if a specific field has validation errors.
     *
     * @param string $field The field name to check.
     *
     * @return bool True if the field has one or more errors.
     */
    public function hasError(string $field): bool
    {
        return isset($this->errors[$field]) && !empty($this->errors[$field]);
    }
}
