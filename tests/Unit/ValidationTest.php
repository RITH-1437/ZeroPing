<?php

namespace Tests\Unit;

use App\Core\Support\Validator;
use App\Core\Testing\TestCase;

class ValidationTest extends TestCase
{
    public function test_can_validate_data()
    {
        $validator = new Validator();

        $data = [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
        ];

        $rules = [
            'name' => 'required',
            'email' => 'required|email',
        ];

        $this->assertTrue($validator->validate($data, $rules));
    }
}
