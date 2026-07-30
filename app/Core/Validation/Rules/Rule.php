<?php

declare(strict_types=1);

namespace App\Core\Validation\Rules;

/**
 * Contract for all validation rules.
 *
 * Each rule implementation is responsible for determining whether
 * a given field value passes its constraint, and for providing
 * a human-readable error message when validation fails.
 */
interface Rule
{
    /**
     * Determine if the given value passes this rule.
     *
     * @param string $field      The name of the field being validated.
     * @param mixed  $value      The value under validation.
     * @param array  $data       The full data array being validated.
     * @param array  $parameters Parameters parsed from the rule definition (e.g., min:8 → ['8']).
     *
     * @return bool True if the value passes validation, false otherwise.
     */
    public function validate(
        string $field,
        mixed $value,
        array $data = [],
        array $parameters = []
    ): bool;

    /**
     * Get the error message for a failed validation.
     *
     * @param string $field      The name of the field that failed validation.
     * @param array  $parameters Parameters parsed from the rule definition.
     *
     * @return string The formatted error message.
     */
    public function message(
        string $field,
        array $parameters = []
    ): string;
}
