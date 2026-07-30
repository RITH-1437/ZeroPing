<?php

declare(strict_types=1);

namespace App\Core\Validation\Rules;

/**
 * Validates that a field value matches a given regular expression pattern.
 *
 * The pattern is passed as the first parameter (e.g., "regex:/^[a-z]+$/i").
 * Uses preg_match() internally. An invalid pattern will cause the rule to fail.
 */
class RegexRule extends AbstractRule
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

        if (empty($parameters)) {
            return true;
        }

        $pattern = $parameters[0];

        // Suppress warnings from invalid patterns and treat them as failures
        $result = @preg_match($pattern, (string) $value);

        return $result === 1;
    }

    /**
     * {@inheritDoc}
     */
    public function message(string $field, array $parameters = []): string
    {
        return "The {$this->formatFieldName($field)} field format is invalid.";
    }
}
