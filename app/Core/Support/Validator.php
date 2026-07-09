<?php

namespace App\Core\Support;

use App\Core\Validation\Validator as NewValidator;

class Validator
{
    protected NewValidator $validator;

    public function __construct(array $data = [], array $rules = [])
    {
        $this->validator = NewValidator::make($data, $rules);
    }

    public function validate(array $data, array $rules): bool
    {
        $this->validator = NewValidator::make($data, $rules);

        return $this->validator->passes();
    }

    public function passes(): bool
    {
        return $this->validator->passes();
    }

    public function errors(): array
    {
        return $this->validator->errors();
    }
}
