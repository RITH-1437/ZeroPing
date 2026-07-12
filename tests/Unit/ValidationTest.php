<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\Validation\ValidationResult;
use App\Core\Validation\RuleParser;
use App\Core\Validation\RuleRegistry;
use App\Core\Validation\Validator;
use App\Core\Validation\Rules\Rule;
use App\Core\Validation\Rules\RequiredRule;
use App\Core\Validation\Rules\MinRule;
use App\Core\Validation\Rules\MaxRule;
use App\Core\Validation\Rules\EmailRule;
use App\Core\Validation\Rules\StringRule;
use App\Core\Validation\Rules\NumericRule;
use App\Core\Validation\Rules\IntegerRule;
use App\Core\Validation\Rules\SameRule;
use App\Core\Validation\Rules\ConfirmedRule;
use App\Core\Validation\Rules\BetweenRule;
use App\Core\Validation\Rules\ArrayRule;
use App\Core\Validation\Rules\InRule;
use App\Core\Validation\Rules\NotInRule;
use App\Core\Validation\Rules\RegexRule;
use App\Core\Config\Config;
use App\Core\Config\ConfigRepository;
use App\Core\Application\App;

class ValidationTest extends \Tests\TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $_SESSION = [];
        $repo = new ConfigRepository();
        $repo->set(['app' => ['name' => 'Test']]);
        Config::setRepository($repo);

        $container = new \App\Core\Container\Container();
        $registry = new RuleRegistry();
        $registry->register('required', new RequiredRule());
        $registry->register('min', new MinRule());
        $registry->register('max', new MaxRule());
        $registry->register('email', new EmailRule());
        $registry->register('string', new StringRule());
        $registry->register('numeric', new NumericRule());
        $registry->register('integer', new IntegerRule());
        $registry->register('same', new SameRule());
        $registry->register('confirmed', new ConfirmedRule());
        $registry->register('between', new BetweenRule());
        $registry->register('array', new ArrayRule());
        $registry->register('in', new InRule());
        $registry->register('not_in', new NotInRule());
        $registry->register('regex', new RegexRule());
        $container->instance(RuleRegistry::class, $registry);

        App::setContainer($container);
    }

    public function testValidationResultPassesWhenNoErrors(): void
    {
        $result = new ValidationResult();

        $this->assertTrue($result->passes());
        $this->assertFalse($result->fails());
        $this->assertSame([], $result->errors());
    }

    public function testValidationResultFailsWhenErrorsAdded(): void
    {
        $result = new ValidationResult();
        $result->add('name', 'Name is required');

        $this->assertFalse($result->passes());
        $this->assertTrue($result->fails());
        $this->assertArrayHasKey('name', $result->errors());
    }

    public function testValidationResultStoresMultipleErrorsPerField(): void
    {
        $result = new ValidationResult();
        $result->add('email', 'Email is required');
        $result->add('email', 'Email is invalid');

        $errors = $result->errors();

        $this->assertCount(2, $errors['email']);
    }

    public function testRuleParserParsesSimpleRule(): void
    {
        $parser = new RuleParser();

        $parsed = $parser->parse('required');

        $this->assertSame('required', $parsed['name']);
        $this->assertSame([], $parsed['parameters']);
    }

    public function testRuleParserParsesRuleWithSingleParameter(): void
    {
        $parser = new RuleParser();

        $parsed = $parser->parse('min:8');

        $this->assertSame('min', $parsed['name']);
        $this->assertSame(['8'], $parsed['parameters']);
    }

    public function testRuleParserParsesRuleWithMultipleParameters(): void
    {
        $parser = new RuleParser();

        $parsed = $parser->parse('between:1,100');

        $this->assertSame('between', $parsed['name']);
        $this->assertSame(['1', '100'], $parsed['parameters']);
    }

    public function testRuleParserTrimsWhitespace(): void
    {
        $parser = new RuleParser();

        $parsed = $parser->parse(' min : 8 ');

        $this->assertSame('min', $parsed['name']);
        $this->assertSame(['8'], $parsed['parameters']);
    }

    public function testRuleRegistryStoresAndRetrievesRules(): void
    {
        $registry = new RuleRegistry();
        $rule = new class implements Rule {
            public function validate(string $field, mixed $value, array $data = [], array $parameters = []): bool
            {
                return true;
            }
            public function message(string $field, array $parameters = []): string
            {
                return 'test';
            }
        };

        $registry->register('test', $rule);

        $this->assertSame($rule, $registry->get('test'));
        $this->assertNull($registry->get('nonexistent'));
    }

    public function testValidatorPassesWithValidData(): void
    {
        $validator = Validator::make(
            ['name' => 'John'],
            ['name' => 'required']
        );

        $this->assertTrue($validator->passes());
        $this->assertFalse($validator->fails());
    }

    public function testValidatorFailsWithMissingRequiredField(): void
    {
        $validator = Validator::make(
            [],
            ['name' => 'required']
        );

        $this->assertTrue($validator->fails());
        $this->assertFalse($validator->passes());
    }

    public function testValidatorReturnsErrors(): void
    {
        $validator = Validator::make(
            [],
            ['name' => 'required']
        );

        $errors = $validator->errors();

        $this->assertArrayHasKey('name', $errors);
    }

    public function testValidatorNullableSkipsValidationWhenEmpty(): void
    {
        $validator = Validator::make(
            ['name' => ''],
            ['name' => 'nullable|required']
        );

        $this->assertTrue($validator->passes());
    }

    public function testValidatorWithCustomMessage(): void
    {
        $validator = Validator::make(
            [],
            ['email' => 'required'],
            ['email.required' => 'Email is mandatory']
        );

        $errors = $validator->errors();

        $this->assertSame('Email is mandatory', $errors['email'][0]);
    }

    public function testValidatorCachesResult(): void
    {
        $validator = Validator::make(
            ['name' => 'John'],
            ['name' => 'required']
        );

        $result1 = $validator->passes();
        $result2 = $validator->passes();

        $this->assertSame($result1, $result2);
    }

    public function testValidatorMinRuleFails(): void
    {
        $validator = Validator::make(
            ['age' => '5'],
            ['age' => 'min:18']
        );

        $this->assertTrue($validator->fails());
    }

    public function testValidatorMinRulePasses(): void
    {
        $validator = Validator::make(
            ['age' => '25'],
            ['age' => 'min:18']
        );

        $this->assertTrue($validator->passes());
    }

    public function testValidatorEmailRuleFails(): void
    {
        $validator = Validator::make(
            ['email' => 'not-an-email'],
            ['email' => 'email']
        );

        $this->assertTrue($validator->fails());
    }

    public function testValidatorEmailRulePasses(): void
    {
        $validator = Validator::make(
            ['email' => 'test@example.com'],
            ['email' => 'email']
        );

        $this->assertTrue($validator->passes());
    }

    public function testValidatorMultipleFields(): void
    {
        $validator = Validator::make(
            ['name' => 'John', 'email' => 'bad'],
            ['name' => 'required', 'email' => 'email']
        );

        $errors = $validator->errors();

        $this->assertArrayNotHasKey('name', $errors);
        $this->assertArrayHasKey('email', $errors);
    }

    public function testValidatorBailOnFirstError(): void
    {
        $validator = Validator::make(
            [],
            ['name' => 'required|min:3']
        );

        $errors = $validator->errors();

        $this->assertCount(1, $errors['name']);
    }
}
