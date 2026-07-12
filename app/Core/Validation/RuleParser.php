<?php

namespace App\Core\Validation;

class RuleParser
{
    public function parse(string $rule): array
    {
        if (!str_contains($rule, ':')) {
            return [

                'name' => $rule,

                'parameters' => [],

            ];
        }

        [$name, $parameters] = explode(':', $rule, 2);

        return [

            'name' => trim($name),

            'parameters' => array_map(
                'trim',
                explode(',', $parameters)
            ),

        ];
    }
}
