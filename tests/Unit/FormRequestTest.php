<?php

namespace Tests\Unit;

use App\Core\Testing\TestCase;
use App\Core\Validation\FormRequest;
use App\Core\Validation\ValidationException;

class TestFormRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|min:2',
            'email' => 'required|email',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Please provide a name.',
        ];
    }
}

class FormRequestTest extends TestCase
{
    public function test_passes_with_valid_data()
    {
        $request = new TestFormRequest([
            'name' => 'John',
            'email' => 'john@example.com',
        ]);

        $this->assertTrue($request->passes());
        $this->assertFalse($request->fails());
    }

    public function test_fails_with_invalid_data()
    {
        $request = new TestFormRequest([
            'name' => '',
            'email' => 'invalid',
        ]);

        $this->assertTrue($request->fails());
        $this->assertFalse($request->passes());
    }

    public function test_validated_returns_clean_data()
    {
        $request = new TestFormRequest([
            'name' => 'John',
            'email' => 'john@example.com',
            'extra' => 'should-not-be-in-validated',
        ]);

        $validated = $request->validated();

        $this->assertArrayHasKey('name', $validated);
        $this->assertArrayHasKey('email', $validated);
        $this->assertArrayNotHasKey('extra', $validated);
    }

    public function test_validated_throws_on_failure()
    {
        $request = new TestFormRequest([
            'name' => '',
            'email' => '',
        ]);

        $threw = false;
        try {
            $request->validated();
        } catch (ValidationException $e) {
            $threw = true;
            $errors = $e->errors();
            $this->assertNotEmpty($errors);
        }

        $this->assertTrue($threw);
    }

    public function test_custom_error_messages()
    {
        $request = new TestFormRequest([
            'name' => '',
            'email' => 'john@example.com',
        ]);

        $request->validate();
        $errors = $request->errors();

        $this->assertStringContainsString('Please provide a name.', $errors['name'][0]);
    }

    public function test_input_method()
    {
        $request = new TestFormRequest([
            'name' => 'John',
        ]);

        $this->assertEquals('John', $request->input('name'));
        $this->assertNull($request->input('nonexistent'));
        $this->assertEquals('default', $request->input('nonexistent', 'default'));
    }

    public function test_all_method()
    {
        $data = ['name' => 'John', 'email' => 'john@example.com'];
        $request = new TestFormRequest($data);

        $this->assertEquals($data, $request->all());
    }
}
