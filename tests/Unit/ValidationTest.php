<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\Application\App;
use App\Core\Validation\FormRequest;
use App\Core\Validation\ValidationException;
use App\Core\Validation\Validator;
use App\Providers\ValidationServiceProvider;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Core\Validation\Validator
 * @covers \App\Core\Validation\FormRequest
 * @covers \App\Core\Validation\ValidationException
 */
class ValidationTest extends TestCase
{
    protected function setUp(): void
    {
        (new ValidationServiceProvider(App::container()))->register();
    }

    // ─── Passing Validation ──────────────────────────────────────────

    public function testPassesForValidData(): void
    {
        $result = Validator::make(
            ['name' => 'Ada', 'email' => 'ada@x.com', 'age' => 20],
            ['name' => 'required|string', 'email' => 'required|email', 'age' => 'required|integer|min:18']
        )->validate();

        $this->assertTrue($result->passes());
        $this->assertSame([], $result->errors());
    }

    // ─── Failing Validation ──────────────────────────────────────────

    public function testFailsRequiredRuleForEmptyValue(): void
    {
        $result = Validator::make(
            ['name' => ''],
            ['name' => 'required']
        )->validate();

        $this->assertTrue($result->fails());
        $this->assertArrayHasKey('name', $result->errors());
    }

    public function testFailsEmailAndMinRules(): void
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

    // ─── In / NotIn Rules ────────────────────────────────────────────

    public function testInRulePassesForAllowedValue(): void
    {
        $this->assertTrue(
            Validator::make(['role' => 'admin'], ['role' => 'in:admin,editor'])->passes()
        );
    }

    public function testInRuleFailsForDisallowedValue(): void
    {
        $this->assertFalse(
            Validator::make(['role' => 'super'], ['role' => 'in:admin,editor'])->passes()
        );
    }

    public function testNotInRuleFailsForExcludedValue(): void
    {
        $this->assertFalse(
            Validator::make(['role' => 'admin'], ['role' => 'not_in:admin'])->passes()
        );
    }

    // ─── Same / Confirmed Rules ──────────────────────────────────────

    public function testSameRulePassesWhenFieldsMatch(): void
    {
        $this->assertTrue(
            Validator::make(
                ['pw' => 'x', 'pw2' => 'x'],
                ['pw' => 'same:pw2']
            )->passes()
        );
    }

    public function testConfirmedRuleFailsWhenConfirmationDiffers(): void
    {
        $this->assertFalse(
            Validator::make(
                ['password' => 'x', 'password_confirmation' => 'y'],
                ['password' => 'confirmed']
            )->passes()
        );
    }

    // ─── Nullable Rule ───────────────────────────────────────────────

    public function testNullableSkipsValidationForEmptyValue(): void
    {
        $this->assertTrue(
            Validator::make(
                ['nickname' => ''],
                ['nickname' => 'nullable|required|string']
            )->passes()
        );
    }

    // ─── Custom Messages ─────────────────────────────────────────────

    public function testCustomMessagesOverrideDefaults(): void
    {
        $result = Validator::make(
            ['name' => ''],
            ['name' => 'required'],
            ['name.required' => 'Custom: name needed']
        )->validate();

        $this->assertSame(['Custom: name needed'], $result->errors()['name']);
    }

    // ─── Bail Behavior ───────────────────────────────────────────────

    public function testBailStopsAfterFirstFailure(): void
    {
        $result = Validator::make(
            ['name' => ''],
            ['name' => 'required|email']
        )->validate();

        $this->assertCount(1, $result->errors()['name']);
    }

    // ─── Helper Function ─────────────────────────────────────────────

    public function testValidatorHelperFunctionReturnsValidatorInstance(): void
    {
        $validator = validator(['email' => 'bad'], ['email' => 'email']);

        $this->assertInstanceOf(Validator::class, $validator);
        $this->assertTrue($validator->fails());
    }

    // ─── FormRequest ─────────────────────────────────────────────────

    public function testFormRequestReturnsValidatedDataOnSuccess(): void
    {
        $request = new class (['name' => 'Ada']) extends FormRequest {
            public function rules(): array
            {
                return ['name' => 'required|string'];
            }
        };

        $this->assertSame(['name' => 'Ada'], $request->validated());
    }

    public function testFormRequestThrowsValidationExceptionOnFailure(): void
    {
        $request = new class (['name' => '']) extends FormRequest {
            public function rules(): array
            {
                return ['name' => 'required|string'];
            }
        };

        $this->expectException(ValidationException::class);
        $request->validated();
    }
}
