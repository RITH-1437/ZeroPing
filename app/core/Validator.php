<?php

class Validator
{
    private array $errors = [];

    public function required(string $field, $value): self
    {
        if (trim($value) === '') {
            $this->errors[$field] =
                "{$field} is required.";
        }

        return $this;
    }

    public function email(string $field, $value): self
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {

            $this->errors[$field] =
                "Invalid email.";
        }

        return $this;
    }

    public function passes(): bool
    {
        return empty($this->errors);
    }

    public function errors(): array
    {
        return $this->errors;
    }
}