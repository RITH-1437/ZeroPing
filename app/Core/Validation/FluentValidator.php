<?php

declare(strict_types=1);

namespace App\Core\Validation;

/**
 * Fluent interface for building and executing validation rules.
 *
 * Provides a chainable API for defining validation rules programmatically
 * without writing raw rule strings. Delegates actual validation to the
 * core Validator class.
 *
 * @example
 * $result = FluentValidator::make($data)
 *     ->field('email')->required()->email()->end()
 *     ->field('name')->required()->string()->max(255)->end()
 *     ->validate();
 */
class FluentValidator
{
    /**
     * The accumulated rules keyed by field name.
     *
     * @var array<string, string[]>
     */
    private array $rules = [];

    /**
     * The data to validate.
     *
     * @var array<string, mixed>
     */
    private array $data = [];

    /**
     * Custom error messages keyed by "field.rule" notation.
     *
     * @var array<string, string>
     */
    private array $messages = [];

    /**
     * Create a new FluentValidator instance.
     *
     * @param array<string, mixed> $data The data to validate.
     */
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    /**
     * Create a new FluentValidator instance statically.
     *
     * @param array<string, mixed> $data The data to validate.
     *
     * @return self
     */
    public static function make(array $data = []): self
    {
        return new self($data);
    }

    /**
     * Set the data to validate.
     *
     * @param array<string, mixed> $data The data array.
     *
     * @return self
     */
    public function setData(array $data): self
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Begin defining rules for a specific field.
     *
     * @param string $name The field name.
     *
     * @return FieldRuleBuilder A builder instance for chaining rules on this field.
     */
    public function field(string $name): FieldRuleBuilder
    {
        return new FieldRuleBuilder($this, $name);
    }

    /**
     * Add a rule for a field with an optional custom message.
     *
     * @param string      $field   The field name.
     * @param string      $rule    The rule definition string (e.g., "min:8").
     * @param string|null $message An optional custom error message.
     *
     * @return self
     */
    public function addRule(string $field, string $rule, ?string $message = null): self
    {
        if (!isset($this->rules[$field])) {
            $this->rules[$field] = [];
        }

        $this->rules[$field][] = $rule;

        if ($message !== null) {
            $ruleName = explode(':', $rule, 2)[0];
            $this->messages["{$field}.{$ruleName}"] = $message;
        }

        return $this;
    }

    /**
     * Get the compiled rules as pipe-delimited strings keyed by field.
     *
     * @return array<string, string> The rules array suitable for the Validator.
     */
    public function getRules(): array
    {
        $result = [];
        foreach ($this->rules as $field => $fieldRules) {
            $result[$field] = implode('|', $fieldRules);
        }
        return $result;
    }

    /**
     * Get all custom error messages.
     *
     * @return array<string, string> The custom messages.
     */
    public function getMessages(): array
    {
        return $this->messages;
    }

    /**
     * Run validation and determine if it passes.
     *
     * @return bool True if validation passes.
     */
    public function passes(): bool
    {
        return $this->validate()->passes();
    }

    /**
     * Run validation and determine if it fails.
     *
     * @return bool True if validation fails.
     */
    public function fails(): bool
    {
        return $this->validate()->fails();
    }

    /**
     * Run validation and return the error messages.
     *
     * @return array<string, string[]> The validation errors.
     */
    public function errors(): array
    {
        return $this->validate()->errors();
    }

    /**
     * Execute validation and return the result.
     *
     * @return ValidationResult The validation result instance.
     */
    public function validate(): ValidationResult
    {
        $validator = new Validator($this->data, $this->getRules(), $this->messages);
        return $validator->validate();
    }
}
