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
        $validator = new Validator($this->data, $this->getRules(), $this->messages);
        $result = $validator->validate();
        return $result;
    }
}
