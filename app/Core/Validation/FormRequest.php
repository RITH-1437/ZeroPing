<?php

namespace App\Core\Validation;

abstract class FormRequest
{
    protected array $data;
    protected ?ValidationResult $validationResult = null;

    public function __construct(?array $data = null)
    {
        $this->data = $data ?? array_merge($_GET, $_POST);
    }

    abstract public function rules(): array;

    public function messages(): array
    {
        return [];
    }

    public function authorize(): bool
    {
        return true;
    }

    public function validated(): array
    {
        $this->validate();

        if ($this->validationResult->fails()) {
            throw new ValidationException($this->validationResult->errors());
        }

        $validated = [];
        foreach (array_keys($this->rules()) as $field) {
            if (array_key_exists($field, $this->data)) {
                $validated[$field] = $this->data[$field];
            }
        }

        return $validated;
    }

    public function validate(): ValidationResult
    {
        if ($this->validationResult !== null) {
            return $this->validationResult;
        }

        $this->validationResult = Validator::make($this->data, $this->rules())->validate();

        return $this->validationResult;
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

    public function input(string $key, mixed $default = null): mixed
    {
        return $this->data[$key] ?? $default;
    }

    public function all(): array
    {
        return $this->data;
    }
}
