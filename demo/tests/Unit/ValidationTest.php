<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\Application\App;
use App\Core\Validation\FormRequest;
use App\Core\Validation\ValidationException;
use App\Core\Validation\Validator;
use App\Providers\ValidationServiceProvider;
use PHPUnit\Framework\TestCase;

class ValidationTest extends TestCase
{
    protected function setUp(): void
    {
        // Wire the built-in rules into the shared container, exactly as the
        // framework does when App boots (ValidationServiceProvider::register).
        (new ValidationServiceProvider(App::container()))->register();
    }

    public function testPassesForValidData(): void
    {
        $result = Validator::make(
            ['name' => 'Ada', 'email' => 'ada@x.com', 'age' => 20],
            ['name' => 'required|string', 'email' => 'required|email', 'age' => 'required|integer|min:18']
        )->validate();

        $this->assertTrue($result->passes());
        $this->assertSame([], $result->errors());
    }

    public function testFailsRequired(): void
    {
        $result = Validator::make(
            ['name' => ''],
            ['name' => 'required']
        )->validate();

        $this->assertTrue($result->fails());
        $this->assertArrayHasKey('name', $result->errors());
    }

    public function testFailsEmailAndMin(): void
    {
        $validator = Validator::make(
            ['email' => 'not-an-email', 'age' => 10],
            ['email' => 'required|email', 'age' => 'required|integer|min:18']
        );

        $this->assertFalse($validator->passes());
        $errors = $validator->errors();
        $this->assertStringContainsString('valid email', implode(' ', $errors['email']));
        $this->assertStringContainsString('at least 18', implode(' ', $errors['age']));
    }

    public function testInAndNotIn(): void
    {
        $this->assertTrue(
            Validator::make(['role' => 'admin'], ['role' => 'in:admin,editor'])->passes()
        );
        $this->assertFalse(
            Validator::make(['role' => 'super'], ['role' => 'in:admin,editor'])->passes()
        );
        $this->assertFalse(
            Validator::make(['role' => 'admin'], ['role' => 'not_in:admin'])->passes()
        );
    }

    public function testSameAndConfirmed(): void
    {
        $this->assertTrue(
            Validator::make(
                ['pw' => 'x', 'pw2' => 'x'],
                ['pw' => 'same:pw2']
            )->passes()
        );
        $this->assertFalse(
            Validator::make(
                ['password' => 'x', 'password_confirmation' => 'y'],
                ['password' => 'confirmed']
            )->passes()
        );
    }

    public function testNullableSkipsEmpty(): void
    {
        $this->assertTrue(
            Validator::make(
                ['nickname' => ''],
                ['nickname' => 'nullable|required|string']
            )->passes()
        );
    }

    public function testCustomMessagesOverride(): void
    {
        $result = Validator::make(
            ['name' => ''],
            ['name' => 'required'],
            ['name.required' => 'Custom: name needed']
        )->validate();

        $this->assertSame(['Custom: name needed'], $result->errors()['name']);
    }

    public function testBailStopsAfterFirstFailure(): void
    {
        $result = Validator::make(
            ['name' => ''],
            ['name' => 'required|email']
        )->validate();

        $this->assertCount(1, $result->errors()['name']);
    }

    public function testValidatorHelperWorks(): void
    {
        $validator = validator(['email' => 'bad'], ['email' => 'email']);

        $this->assertInstanceOf(Validator::class, $validator);
        $this->assertTrue($validator->fails());
    }

    public function testFormRequestValidatesAndThrows(): void
    {
        $good = new class (['name' => 'Ada']) extends FormRequest {
            public function rules(): array
            {
                return ['name' => 'required|string'];
            }
        };
        $this->assertSame(['name' => 'Ada'], $good->validated());

        $bad = new class (['name' => '']) extends FormRequest {
            public function rules(): array
            {
                return ['name' => 'required|string'];
            }
        };

        $this->expectException(ValidationException::class);
        $bad->validated();
    }
}
