<?php

declare(strict_types=1);

namespace App\Core\Validation;

use App\Core\Application\App;

/**
 * Core validation engine.
 *
 * Validates a data array against a set of rules defined as pipe-delimited
 * strings (e.g., "required|email|max:255"). Supports custom error messages,
 * nullable fields, and bail-on-first-failure behavior.
 *
 * @example
 *   $validator = Validator::make($request->all(), [
 *       'email' => 'required|email|max:255',
 *       'name'  => 'required|string|min:2',
 *   ]);
 *
 *   if ($validator->fails()) {
 *       $errors = $validator->errors();
 *   }
 *
 * @since 1.0.0
 * @author Rin Nairith
 * @link https://zero-ping.duckdns.org/docs/validation
 */
class Validator
{
    /**
     * The validation result instance.
     */
    protected ValidationResult $result;

    /**
     * Whether validation has already been performed.
     */
    protected bool $validated = false;

    /**
     * Create a new validator instance.
     *
     * @param array<string, mixed>                      $data     The data to validate.
     * @param array<string, string|array<int, string>>  $rules    The validation rules keyed by field name.
     * @param array<string, string>                     $messages Custom error messages keyed by "field.rule" notation.
     */
    public function __construct(
        protected array $data,
        protected array $rules,
        protected array $messages = []
    ) {
        $this->result = new ValidationResult();
    }

    /**
     * Create a new validator instance statically.
     *
     * @param array<string, mixed>                      $data     The data to validate.
     * @param array<string, string|array<int, string>>  $rules    The validation rules keyed by field name.
     * @param array<string, string>                     $messages Custom error messages keyed by "field.rule" notation.
     *
     * @return static
     */
    public static function make(
        array $data,
        array $rules,
        array $messages = []
    ): static {
        return new static($data, $rules, $messages);
    }

    /**
     * Run validation against the data and rules.
     *
     * Each field's rule string is split on "|" and each rule is evaluated
     * in order. If a field is marked as "nullable" and the value is empty,
     * all rules for that field are skipped. Validation bails on the first
     * failed rule for each field.
     *
     * @return ValidationResult The result containing any validation errors.
     */
    public function validate(): ValidationResult
    {
        if ($this->validated) {
            return $this->result;
        }

        $this->validated = true;

        $registry = App::container()->make(RuleRegistry::class);
        $parser = new RuleParser();

        foreach ($this->rules as $field => $ruleString) {
            $value = $this->resolveValue($field);
            $rulesArray = $this->splitRules($ruleString);

            // Skip validation when the field is nullable and the value is empty
            if ($this->isNullable($rulesArray) && $this->isEffectivelyEmpty($value)) {
                continue;
            }

            foreach ($rulesArray as $rule) {
                // Skip meta-rules that don't perform actual validation
                if ($this->isMetaRule($rule)) {
                    continue;
                }

                $parsed = $parser->parse($rule);

                // Skip empty rule names (edge case from trailing pipes)
                if ($parsed['name'] === '') {
                    continue;
                }

                $ruleInstance = $registry->get($parsed['name']);

                // Skip unregistered rules silently
                if ($ruleInstance === null) {
                    continue;
                }

                if (!$ruleInstance->validate($field, $value, $this->data, $parsed['parameters'])) {
                    $message = $this->resolveMessage($field, $parsed['name'], $ruleInstance, $parsed['parameters']);
                    $this->result->add($field, $message);

                    // Bail: stop validating this field after the first failed rule
                    break;
                }
            }
        }

        return $this->result;
    }

    /**
     * Determine if validation passes.
     *
     * @return bool True if there are no validation errors.
     */
    public function passes(): bool
    {
        return $this->validate()->passes();
    }

    /**
     * Determine if validation fails.
     *
     * @return bool True if there are validation errors.
     */
    public function fails(): bool
    {
        return $this->validate()->fails();
    }

    /**
     * Get the validation error messages.
     *
     * @return array<string, string[]> The errors keyed by field name.
     */
    public function errors(): array
    {
        return $this->validate()->errors();
    }

    /**
     * Get the validated data (only fields that have rules).
     *
     * @return array<string, mixed> The subset of data that was validated.
     *
     * @throws ValidationException If validation fails.
     */
    public function validated(): array
    {
        if ($this->fails()) {
            throw new ValidationException($this->errors());
        }

        $validated = [];
        foreach (array_keys($this->rules) as $field) {
            if (array_key_exists($field, $this->data)) {
                $validated[$field] = $this->data[$field];
            }
        }

        return $validated;
    }

    /**
     * Resolve the value for a given field from the data array.
     *
     * @param string $field The field name.
     *
     * @return mixed The field value or null if not present.
     */
    protected function resolveValue(string $field): mixed
    {
        return $this->data[$field] ?? null;
    }

    /**
     * Split a pipe-delimited rule string (or array) into individual rule definitions.
     *
     * @param string|array<int, string> $ruleString The pipe-delimited rule string or array of rules.
     *
     * @return string[] The individual rule strings.
     */
    protected function splitRules(string|array $ruleString): array
    {
        if (is_array($ruleString)) {
            return $ruleString;
        }

        return array_filter(
            array_map('trim', explode('|', $ruleString)),
            static fn(string $rule): bool => $rule !== ''
        );
    }

    /**
     * Determine if the rules array includes the "nullable" meta-rule.
     *
     * @param string[] $rules The array of rule strings.
     *
     * @return bool True if nullable is present.
     */
    protected function isNullable(array $rules): bool
    {
        return in_array('nullable', $rules, true);
    }

    /**
     * Determine if a value is effectively empty (null or empty string).
     *
     * @param mixed $value The value to check.
     *
     * @return bool True if the value is null or an empty string.
     */
    protected function isEffectivelyEmpty(mixed $value): bool
    {
        return $value === null || $value === '';
    }

    /**
     * Determine if a rule is a meta-rule that does not perform validation itself.
     *
     * @param string $rule The rule string.
     *
     * @return bool True if the rule is a meta-rule.
     */
    protected function isMetaRule(string $rule): bool
    {
        return in_array($rule, ['nullable', 'bail'], true);
    }

    /**
     * Resolve the error message for a failed rule.
     *
     * Checks for a custom message first, then falls back to the rule's default message.
     *
     * @param string                              $field      The field name.
     * @param string                              $ruleName   The rule name.
     * @param \App\Core\Validation\Rules\Rule     $rule       The rule instance.
     * @param array<int, string>                   $parameters The rule parameters.
     *
     * @return string The resolved error message.
     */
    protected function resolveMessage(
        string $field,
        string $ruleName,
        \App\Core\Validation\Rules\Rule $rule,
        array $parameters
    ): string {
        // Check for field-specific custom message
        if (isset($this->messages["{$field}.{$ruleName}"])) {
            return $this->messages["{$field}.{$ruleName}"];
        }

        // Check for rule-level custom message (applies to all fields)
        if (isset($this->messages[$ruleName])) {
            return $this->messages[$ruleName];
        }

        return $rule->message($field, $parameters);
    }
}
