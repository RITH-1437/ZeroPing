<?php

namespace App\Core\Validation;

class FluentValidator
{
    private array $rules = [];
    private array $data = [];
    private array $messages = [];

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public static function make(array $data = []): self
    {
        return new self($data);
    }

    public function setData(array $data): self
    {
        $this->data = $data;
        return $this;
    }

    public function field(string $name): FieldRuleBuilder
    {
        return new FieldRuleBuilder($this, $name);
    }

    public function addRule(string $field, string $rule, ?string $message = null): self
    {
        if (!isset($this->rules[$field])) {
            $this->rules[$field] = [];
        }
        $this->rules[$field][] = $rule;

        if ($message !== null) {
            $this->messages[$field . '.' . explode(':', $rule)[0]] = $message;
        }

        return $this;
    }

    public function getRules(): array
    {
        $result = [];
        foreach ($this->rules as $field => $fieldRules) {
            $result[$field] = implode('|', $fieldRules);
        }
        return $result;
    }

    public function getMessages(): array
    {
        return $this->messages;
    }

    public function passes(): bool
    {
        return $this->validate()->passes();
    }

    public function fails(): bool
    {
        return $this->validate()->fails();
    }

    public function errors(): array
    {
        return $this->validate()->errors();
    }

    public function validate(): ValidationResult
    {
        $validator = new Validator($this->data, $this->getRules());
        $result = $validator->validate();
        return $result;
    }
}

class FieldRuleBuilder
{
    private FluentValidator $validator;
    private string $field;
    private array $rules = [];

    public function __construct(FluentValidator $validator, string $field)
    {
        $this->validator = $validator;
        $this->field = $field;
    }

    public function required(string $message = null): self
    {
        $this->validator->addRule($this->field, 'required', $message);
        return $this;
    }

    public function string(string $message = null): self
    {
        $this->validator->addRule($this->field, 'string', $message);
        return $this;
    }

    public function email(string $message = null): self
    {
        $this->validator->addRule($this->field, 'email', $message);
        return $this;
    }

    public function numeric(string $message = null): self
    {
        $this->validator->addRule($this->field, 'numeric', $message);
        return $this;
    }

    public function integer(string $message = null): self
    {
        $this->validator->addRule($this->field, 'integer', $message);
        return $this;
    }

    public function min(int $value, string $message = null): self
    {
        $this->validator->addRule($this->field, "min:{$value}", $message);
        return $this;
    }

    public function max(int $value, string $message = null): self
    {
        $this->validator->addRule($this->field, "max:{$value}", $message);
        return $this;
    }

    public function between(int $min, int $max, string $message = null): self
    {
        $this->validator->addRule($this->field, "between:{$min},{$max}", $message);
        return $this;
    }

    public function array(string $message = null): self
    {
        $this->validator->addRule($this->field, 'array', $message);
        return $this;
    }

    public function file(string $message = null): self
    {
        $this->validator->addRule($this->field, 'file', $message);
        return $this;
    }

    public function image(string $message = null): self
    {
        $this->validator->addRule($this->field, 'image', $message);
        return $this;
    }

    public function mimes(string $types, string $message = null): self
    {
        $this->validator->addRule($this->field, "mimes:{$types}", $message);
        return $this;
    }

    public function size(int $kb, string $message = null): self
    {
        $this->validator->addRule($this->field, "size:{$kb}", $message);
        return $this;
    }

    public function in(array $values, string $message = null): self
    {
        $this->validator->addRule($this->field, 'in:' . implode(',', $values), $message);
        return $this;
    }

    public function notIn(array $values, string $message = null): self
    {
        $this->validator->addRule($this->field, 'not_in:' . implode(',', $values), $message);
        return $this;
    }

    public function regex(string $pattern, string $message = null): self
    {
        $this->validator->addRule($this->field, "regex:{$pattern}", $message);
        return $this;
    }

    public function same(string $otherField, string $message = null): self
    {
        $this->validator->addRule($this->field, "same:{$otherField}", $message);
        return $this;
    }

    public function confirmed(string $message = null): self
    {
        $this->validator->addRule($this->field, 'confirmed', $message);
        return $this;
    }

    public function unique(string $table, string $column = null, string $except = null, string $idColumn = null): self
    {
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
        $this->validator->addRule($this->field, "unique:{$params}");
        return $this;
    }

    public function exists(string $table, string $column = null): self
    {
        $params = $table;
        if ($column !== null) {
            $params .= ',' . $column;
        }
        $this->validator->addRule($this->field, "exists:{$params}");
        return $this;
    }

    public function end(): FluentValidator
    {
        return $this->validator;
    }
}
