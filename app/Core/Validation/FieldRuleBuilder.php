<?php

declare(strict_types=1);

namespace App\Core\Validation;

/**
 * Fluent builder for defining validation rules on a single field.
 *
 * Provides a chainable API for adding rules to a specific field within
 * a FluentValidator instance. Call `end()` to return to the parent validator.
 *
 * @example
 * $validator->field('email')
 *     ->required()
 *     ->email()
 *     ->max(255)
 *     ->end();
 */
class FieldRuleBuilder
{
    /**
     * The parent FluentValidator instance.
     */
    private FluentValidator $validator;

    /**
     * The field name being configured.
     */
    private string $field;

    /**
     * Create a new FieldRuleBuilder instance.
     *
     * @param FluentValidator $validator The parent fluent validator.
     * @param string          $field     The field name to build rules for.
     */
    public function __construct(FluentValidator $validator, string $field)
    {
        $this->validator = $validator;
        $this->field = $field;
    }

    /**
     * Mark the field as required.
     *
     * @param string|null $message Custom error message.
     *
     * @return self
     */
    public function required(?string $message = null): self
    {
        return $this->addRule('required', $message);
    }

    /**
     * Mark the field as nullable (skip validation when empty).
     *
     * @return self
     */
    public function nullable(): self
    {
        return $this->addRule('nullable');
    }

    /**
     * The field must be a string.
     *
     * @param string|null $message Custom error message.
     *
     * @return self
     */
    public function string(?string $message = null): self
    {
        return $this->addRule('string', $message);
    }

    /**
     * The field must be a valid email address.
     *
     * @param string|null $message Custom error message.
     *
     * @return self
     */
    public function email(?string $message = null): self
    {
        return $this->addRule('email', $message);
    }

    /**
     * The field must be numeric.
     *
     * @param string|null $message Custom error message.
     *
     * @return self
     */
    public function numeric(?string $message = null): self
    {
        return $this->addRule('numeric', $message);
    }

    /**
     * The field must be an integer.
     *
     * @param string|null $message Custom error message.
     *
     * @return self
     */
    public function integer(?string $message = null): self
    {
        return $this->addRule('integer', $message);
    }

    /**
     * The field must meet a minimum value or length.
     *
     * @param int         $value   The minimum value or length.
     * @param string|null $message Custom error message.
     *
     * @return self
     */
    public function min(int $value, ?string $message = null): self
    {
        return $this->addRule("min:{$value}", $message);
    }

    /**
     * The field must not exceed a maximum value or length.
     *
     * @param int         $value   The maximum value or length.
     * @param string|null $message Custom error message.
     *
     * @return self
     */
    public function max(int $value, ?string $message = null): self
    {
        return $this->addRule("max:{$value}", $message);
    }

    /**
     * The field must be between the given min and max values.
     *
     * @param int         $min     The minimum value or length.
     * @param int         $max     The maximum value or length.
     * @param string|null $message Custom error message.
     *
     * @return self
     */
    public function between(int $min, int $max, ?string $message = null): self
    {
        return $this->addRule("between:{$min},{$max}", $message);
    }

    /**
     * The field must be an array.
     *
     * @param string|null $message Custom error message.
     *
     * @return self
     */
    public function array(?string $message = null): self
    {
        return $this->addRule('array', $message);
    }

    /**
     * The field must be a valid uploaded file.
     *
     * @param string|null $message Custom error message.
     *
     * @return self
     */
    public function file(?string $message = null): self
    {
        return $this->addRule('file', $message);
    }

    /**
     * The field must be an image file.
     *
     * @param string|null $message Custom error message.
     *
     * @return self
     */
    public function image(?string $message = null): self
    {
        return $this->addRule('image', $message);
    }

    /**
     * The file must have one of the given MIME type extensions.
     *
     * @param string      $types   Comma-separated list of allowed extensions.
     * @param string|null $message Custom error message.
     *
     * @return self
     */
    public function mimes(string $types, ?string $message = null): self
    {
        return $this->addRule("mimes:{$types}", $message);
    }

    /**
     * The field must not exceed the given size in kilobytes.
     *
     * @param int         $kb      Maximum size in kilobytes.
     * @param string|null $message Custom error message.
     *
     * @return self
     */
    public function size(int $kb, ?string $message = null): self
    {
        return $this->addRule("size:{$kb}", $message);
    }

    /**
     * The field must be one of the given values.
     *
     * @param array<int, string> $values  The allowed values.
     * @param string|null        $message Custom error message.
     *
     * @return self
     */
    public function in(array $values, ?string $message = null): self
    {
        return $this->addRule('in:' . implode(',', $values), $message);
    }

    /**
     * The field must not be one of the given values.
     *
     * @param array<int, string> $values  The disallowed values.
     * @param string|null        $message Custom error message.
     *
     * @return self
     */
    public function notIn(array $values, ?string $message = null): self
    {
        return $this->addRule('not_in:' . implode(',', $values), $message);
    }

    /**
     * The field must match the given regular expression pattern.
     *
     * @param string      $pattern The regex pattern.
     * @param string|null $message Custom error message.
     *
     * @return self
     */
    public function regex(string $pattern, ?string $message = null): self
    {
        return $this->addRule("regex:{$pattern}", $message);
    }

    /**
     * The field must have the same value as another field.
     *
     * @param string      $otherField The other field name to compare against.
     * @param string|null $message    Custom error message.
     *
     * @return self
     */
    public function same(string $otherField, ?string $message = null): self
    {
        return $this->addRule("same:{$otherField}", $message);
    }

    /**
     * The field must have a matching "{field}_confirmation" field.
     *
     * @param string|null $message Custom error message.
     *
     * @return self
     */
    public function confirmed(?string $message = null): self
    {
        return $this->addRule('confirmed', $message);
    }

    /**
     * The field must be unique in the given database table.
     *
     * @param string      $table    The database table name.
     * @param string|null $column   The column to check (defaults to field name).
     * @param string|null $except   An ID value to exclude (for updates).
     * @param string|null $idColumn The ID column name (defaults to "id").
     *
     * @return self
     */
    public function unique(
        string $table,
        ?string $column = null,
        ?string $except = null,
        ?string $idColumn = null
    ): self {
        $params = $table;
        if ($column !== null) {
            $params .= ',' . $column;
            if ($except !== null) {
                $params .= ',' . $except;
                if ($idColumn !== null) {
                    $params .= ',' . $idColumn;
                }
            }
        }
        return $this->addRule("unique:{$params}");
    }

    /**
     * The field value must exist in the given database table.
     *
     * @param string      $table  The database table name.
     * @param string|null $column The column to check (defaults to field name).
     *
     * @return self
     */
    public function exists(string $table, ?string $column = null): self
    {
        $params = $table;
        if ($column !== null) {
            $params .= ',' . $column;
        }
        return $this->addRule("exists:{$params}");
    }

    /**
     * End building rules for this field and return to the parent validator.
     *
     * @return FluentValidator The parent fluent validator instance.
     */
    public function end(): FluentValidator
    {
        return $this->validator;
    }

    /**
     * Add a rule to the parent validator for this field.
     *
     * @param string      $rule    The rule definition string.
     * @param string|null $message Optional custom error message.
     *
     * @return self
     */
    private function addRule(string $rule, ?string $message = null): self
    {
        $this->validator->addRule($this->field, $rule, $message);
        return $this;
    }
}
