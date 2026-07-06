<?php

namespace App\Core\Validation;

use App\Core\Validation\Rules\Rule;

class RuleRegistry
{
    /**
     * @var array<string, Rule>
     */
    protected array $rules = [];

    public function register(
        string $name,
        Rule $rule
    ): void {
        $this->rules[$name] = $rule;
    }

    public function has(string $name): bool
    {
        return isset($this->rules[$name]);
    }

    public function get(string $name): ?Rule
    {
        return $this->rules[$name] ?? null;
    }

    public function all(): array
    {
        return $this->rules;
    }
}