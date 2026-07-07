<?php

namespace App\Core\Support;

class Validator
{
    /**
     * The data under validation.
     *
     * @var array
     */
    protected array $data = [];

    /**
     * The validation rules.
     *
     * @var array
     */
    protected array $rules = [];

    /**
     * The validation errors.
     *
     * @var array
     */
    protected array $errors = [];

    /**
     * Create a new validator instance.
     *
     * @param  array  $data
     * @param  array  $rules
     * @return void
     */
    public function __construct(array $data = [], array $rules = [])
    {
        $this->data = $data;
        $this->rules = $rules;
    }

    /**
     * Validate the given data against the rules.
     *
     * @param  array  $data
     * @param  array  $rules
     * @return bool
     */
    public function validate(array $data, array $rules): bool
    {
        $this->data = $data;
        $this->rules = $rules;

        foreach ($this->rules as $field => $rule) {
            $rules = explode('|', $rule);

            foreach ($rules as $singleRule) {
                $this->applyRule($field, $singleRule);
            }
        }

        return $this->passes();
    }

    /**
     * Apply a validation rule to a field.
     *
     * @param  string  $field
     * @param  string  $rule
     * @return void
     */
    protected function applyRule(string $field, string $rule): void
    {
        $method = "validate{$rule}";

        if (method_exists($this, $method)) {
            $this->{$method}($field, $this->data[$field] ?? null);
        }
    }

    /**
     * Validate that a field is required.
     *
     * @param  string  $field
     * @param  mixed  $value
     * @return void
     */
    protected function validateRequired(string $field, $value): void
    {
        if (is_null($value) || (is_string($value) && trim($value) === '')) {
            $this->addError($field, 'The ' . str_replace('_', ' ', $field) . ' field is required.');
        }
    }

    /**
     * Validate that a field is a valid email address.
     *
     * @param  string  $field
     * @param  mixed  $value
     * @return void
     */
    protected function validateEmail(string $field, $value): void
    {
        if (! filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->addError($field, 'The ' . str_replace('_', ' ', $field) . ' must be a valid email address.');
        }
    }

    /**
     * Validate the minimum length of a string.
     *
     * @param  string  $field
     * @param  mixed  $value
     * @param  int  $min
     * @return void
     */
    protected function validateMin(string $field, $value, int $min): void
    {
        if (strlen($value) < $min) {
            $this->addError($field, 'The ' . str_replace('_', ' ', $field) . " must be at least {$min} characters.");
        }
    }

    /**
     * Validate that a field is numeric.
     *
     * @param  string  $field
     * @param  mixed  $value
     * @return void
     */
    protected function validateNumeric(string $field, $value): void
    {
        if (! is_numeric($value)) {
            $this->addError($field, 'The ' . str_replace('_', ' ', $field) . ' must be a number.');
        }
    }

    /**
     * Validate that a field is a valid URL.
     *
     * @param  string  $field
     * @param  mixed  $value
     * @return void
     */
    protected function validateUrl(string $field, $value): void
    {
        if (! filter_var($value, FILTER_VALIDATE_URL)) {
            $this->addError($field, 'The ' . str_replace('_', ' ', $field) . ' must be a valid URL.');
        }
    }

    /**
     * Validate that a field is confirmed.
     *
     * @param  string  $field
     * @param  mixed  $value
     * @return void
     */
    protected function validateConfirmed(string $field, $value): void
    {
        if ($value !== ($this->data[$field . '_confirmation'] ?? null)) {
            $this->addError($field, 'The ' . str_replace('_', ' ', $field) . ' confirmation does not match.');
        }
    }

    /**
     * Add an error message for a field.
     *
     * @param  string  $field
     * @param  string  $message
     * @return void
     */
    protected function addError(string $field, string $message): void
    {
        $this->errors[$field][] = $message;
    }

    /**
     * Determine if the validation passes.
     *
     * @return bool
     */
    public function passes(): bool
    {
        return empty($this->errors);
    }

    /**
     * Get the validation errors.
     *
     * @return array
     */
    public function errors(): array
    {
        return $this->errors;
    }
}
