<?php

declare(strict_types=1);

namespace App\Core\Validation;

use App\Core\Validation\Rules\Rule;

/**
 * Registry of available validation rules.
 *
 * Maintains a mapping of rule names to their implementations.
 * Rules are registered at application boot time via service providers
 * and resolved by the Validator during validation.
 */
class RuleRegistry
{
    /**
     * The registered validation rules.
     *
     * @var array<string, Rule>
     */
    protected array $rules = [];

    /**
     * Register a validation rule by name.
     *
     * @param string $name The rule name as used in rule definitions (e.g., "required", "min").
     * @param Rule   $rule The rule implementation instance.
     *
     * @return void
     */
    public function register(string $name, Rule $rule): void
    {
        $this->rules[$name] = $rule;
    }

    /**
     * Retrieve a registered rule by name.
     *
     * @param string $name The rule name to look up.
     *
     * @return Rule|null The rule instance, or null if not registered.
     */
    public function get(string $name): ?Rule
    {
        return $this->rules[$name] ?? null;
    }

    /**
     * Determine if a rule is registered.
     *
     * @param string $name The rule name to check.
     *
     * @return bool True if the rule is registered.
     */
    public function has(string $name): bool
    {
        return isset($this->rules[$name]);
    }

    /**
     * Get all registered rule names.
     *
     * @return string[] The list of registered rule names.
     */
    public function names(): array
    {
        return array_keys($this->rules);
    }
}
