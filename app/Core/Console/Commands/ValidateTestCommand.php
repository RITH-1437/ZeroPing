<?php

namespace App\Core\Console\Commands;

use App\Core\Console\Command;
use App\Core\Validation\Validator;

class ValidateTestCommand extends Command
{
    public function handle(): void
    {
        $data = [

            'name' => 123,

            'username' => 'ab',

            'email' => 'invalid-email',

            'age' => 'abc',

            'password' => 'secret123',

            'password_confirmation' => 'secret',

            'nickname' => '',

        ];

        $validator = Validator::make([

            'email' => 'admin@gmail.com'

        ], [

            'email' => 'unique:users,email'

        ]);

        if ($validator->fails()) {

            print_r($validator->errors());

            return;
        }

        echo "Validation passed." . PHP_EOL;
    }
}