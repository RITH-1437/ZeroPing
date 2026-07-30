<?php

declare(strict_types=1);

namespace App\Core\Validation;

/**
 * Parses rule definition strings into their component name and parameters.
 *
 * Handles the format "rule_name:param1,param2" and various edge cases
 * including empty strings, whitespace, and regex patterns containing colons.
 */
class RuleParser
{
    /**
     * Parse a rule string into its name and parameters.
     *
     * Supports the format "name:param1,param2,...".
     * For rules without parameters, returns an empty parameters array.
     * Handles edge cases like empty strings, whitespace, and regex patterns.
     *
     * @param string $rule The rule definition string to parse.
     *
     * @return array{name: string, parameters: array<int, string>} The parsed rule with name and parameters.
     */
    public function parse(string $rule): array
    {
        $rule = trim($rule);

        // Handle empty or whitespace-only rule strings
        if ($rule === '') {
            return [
                'name' => '',
                'parameters' => [],
            ];
        }

        // Rules without parameters
        if (!str_contains($rule, ':')) {
            return [
                'name' => $rule,
                'parameters' => [],
            ];
        }

        // Special handling for regex rule to preserve the full pattern
        // Regex patterns may contain colons (e.g., regex:/^https?:\/\//)
        if (str_starts_with($rule, 'regex:')) {
            return [
                'name' => 'regex',
                'parameters' => [substr($rule, 6)],
            ];
        }

        // Standard rule parsing: split on first colon only
        [$name, $parameterString] = explode(':', $rule, 2);

        $name = trim($name);

        // Handle empty parameter string after colon (e.g., "required:")
        if ($parameterString === '') {
            return [
                'name' => $name,
                'parameters' => [],
            ];
        }

        $parameters = array_map('trim', explode(',', $parameterString));

        // Filter out empty parameters caused by trailing commas
        $parameters = array_values(array_filter($parameters, static function (string $param): bool {
            return $param !== '';
        }));

        return [
            'name' => $name,
            'parameters' => $parameters,
        ];
    }
}
