<?php

namespace App\Providers;

use App\Core\Validation\DatabasePresenceVerifier;
use App\Core\Validation\RuleRegistry;
use App\Core\Validation\Rules\BetweenRule;
use App\Core\Validation\Rules\ConfirmedRule;
use App\Core\Validation\Rules\EmailRule;
use App\Core\Validation\Rules\ExistsRule;
use App\Core\Validation\Rules\IntegerRule;
use App\Core\Validation\Rules\MaxRule;
use App\Core\Validation\Rules\MinRule;
use App\Core\Validation\Rules\NumericRule;
use App\Core\Validation\Rules\RequiredRule;
use App\Core\Validation\Rules\SameRule;
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