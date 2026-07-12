<?php

namespace App\Core\Validation;

use App\Core\Application\App;

class Validator
{
    protected ValidationResult $result;

    protected bool $validated = false;

    public function __construct(
        protected array $data,
        protected array $rules,
        protected array $messages = []
    ) {
        $this->result = new ValidationResult();
    }

    public static function make(
        array $data,
        array $rules,
        array $messages = []
    ): static {
        return new static($data, $rules, $messages);
    }

    public function validate(): ValidationResult
    {
        if ($this->validated) {
            return $this->result;
        }

        $this->validated = true;

        $registry = App::container()->make(
            RuleRegistry::class
        );

        $parser = new RuleParser();

        foreach ($this->rules as $field => $ruleString) {
            $value = $this->data[$field] ?? null;

            $rulesArray = explode('|', $ruleString);

            /*
            |--------------------------------------------------------------------------
            | Nullable
            |--------------------------------------------------------------------------
            |
            | Skip validation when the field is nullable
            | and the value is empty.
            |
            */

            if (
                in_array('nullable', $rulesArray, true) &&
                ($value === null || $value === '')
            ) {
                continue;
            }

            foreach ($rulesArray as $rule) {
                $parsed = $parser->parse($rule);

                $validator = $registry->get(
                    $parsed['name']
                );

                /*
                |--------------------------------------------------------------------------
                | Unknown Rule
                |--------------------------------------------------------------------------
                */

                if ($validator === null) {
                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | Validate Rule
                |--------------------------------------------------------------------------
                */

                if (
                    !$validator->validate(
                        $field,
                        $value,
                        $this->data,
                        $parsed['parameters']
                    )
                ) {
                    $message = $this->messages["{$field}.{$parsed['name']}"]
                        ?? $validator->message($field, $parsed['parameters']);

                    $this->result->add(
                        $field,
                        $message
                    );

                    /*
                    |--------------------------------------------------------------------------
                    | Bail
                    |--------------------------------------------------------------------------
                    |
                    | Stop validating this field after the
                    | first failed rule.
                    |
                    */

                    break;
                }
            }
        }

        return $this->result;
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
}
