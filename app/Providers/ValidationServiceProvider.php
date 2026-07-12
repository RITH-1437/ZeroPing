<?php

namespace App\Providers;

use App\Core\Validation\DatabasePresenceVerifier;
use App\Core\Validation\RuleRegistry;
use App\Core\Validation\Rules\ArrayRule;
use App\Core\Validation\Rules\BetweenRule;
use App\Core\Validation\Rules\ConfirmedRule;
use App\Core\Validation\Rules\EmailRule;
use App\Core\Validation\Rules\ExistsRule;
use App\Core\Validation\Rules\FileRule;
use App\Core\Validation\Rules\ImageRule;
use App\Core\Validation\Rules\InRule;
use App\Core\Validation\Rules\IntegerRule;
use App\Core\Validation\Rules\MaxRule;
use App\Core\Validation\Rules\MinRule;
use App\Core\Validation\Rules\MimesRule;
use App\Core\Validation\Rules\NotInRule;
use App\Core\Validation\Rules\NumericRule;
use App\Core\Validation\Rules\RegexRule;
use App\Core\Validation\Rules\RequiredRule;
use App\Core\Validation\Rules\SameRule;
use App\Core\Validation\Rules\SizeRule;
use App\Core\Validation\Rules\StringRule;
use App\Core\Validation\Rules\UniqueRule;

class ValidationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $registry = new RuleRegistry();

        $registry->register('required', new RequiredRule());
        $registry->register('min', new MinRule());
        $registry->register('max', new MaxRule());
        $registry->register('email', new EmailRule());
        $registry->register('string', new StringRule());
        $registry->register('numeric', new NumericRule());
        $registry->register('integer', new IntegerRule());
        $registry->register('unique', new UniqueRule());
        $registry->register('exists', new ExistsRule());
        $registry->register('same', new SameRule());
        $registry->register('confirmed', new ConfirmedRule());
        $registry->register('between', new BetweenRule());
        $registry->register('array', new ArrayRule());
        $registry->register('file', new FileRule());
        $registry->register('image', new ImageRule());
        $registry->register('mimes', new MimesRule());
        $registry->register('size', new SizeRule());
        $registry->register('in', new InRule());
        $registry->register('not_in', new NotInRule());
        $registry->register('regex', new RegexRule());

        $this->container->singleton(
            DatabasePresenceVerifier::class,
            fn () => new DatabasePresenceVerifier()
        );

        $this->container->singleton(
            RuleRegistry::class,
            fn () => $registry
        );
    }
}
