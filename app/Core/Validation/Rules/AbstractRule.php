<?php

declare(strict_types=1);

namespace App\Core\Validation\Rules;

/**
 * Base class for validation rules providing common utility methods.
 *
 * All concrete rule implementations should extend this class
 * to leverage shared helper logic for emptiness checks,
 * size calculations, and parameter extraction.
 */
abstract class AbstractRule implements Rule
{
    /**
     * Determine if the given value is considered "empty" for validation purposes.
     *
     * A value is empty when it is null, an empty string, or an empty array.
     *
     * @param mixed $value The value to check.
     *
     * @return bool True if the value is considered empty.
     */
    protected function isEmpty(mixed $value): bool
    {
        return $value === null
            || $value === ''
            || (is_array($value) && empty($value));
    }

    /**
     * Get the character length of the given value.
     *
     * Casts the value to a string and uses multibyte-safe measurement.
     *
     * @param mixed $value The value whose length to measure.
     *
     * @return int The character length.
     */
    protected function length(mixed $value): int
    {
        return mb_strlen((string) $value);
    }

    /**
     * Retrieve a parameter by its index with an optional default.
     *
     * @param array $parameters The parsed parameters array.
     * @param int   $index      The zero-based index of the desired parameter.
     * @param mixed $default    The default value if the parameter is not set.
     *
     * @return mixed The parameter value or the default.
     */
    protected function parameter(
        array $parameters,
        int $index,
        mixed $default = null
    ): mixed {
        return $parameters[$index] ?? $default;
    }

    /**
     * Get the effective size of a value based on its type.
     *
     * For numeric values, returns the numeric value itself.
     * For arrays, returns the element count.
     * For strings, returns the character length.
     *
     * @param mixed $value The value whose size to compute.
     *
     * @return float|int The computed size.
     */
    protected function getSize(mixed $value): float|int
    {
        if (is_numeric($value)) {
            return (float) $value;
        }

        if (is_array($value)) {
            return count($value);
        }

        return $this->length($value);
    }

    /**
     * Format a field name for display in error messages.
     *
     * Replaces underscores and hyphens with spaces for readability.
     *
     * @param string $field The raw field name.
     *
     * @return string The human-readable field name.
     */
    protected function formatFieldName(string $field): string
    {
        return str_replace(['_', '-'], ' ', $field);
    }
}
