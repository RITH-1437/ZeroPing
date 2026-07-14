<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\Container\Container;
use App\Providers\ServiceProvider;
use PHPUnit\Framework\TestCase;

class DefService
{
}

class DefProvider extends ServiceProvider
{
    public static bool $booted = false;

    public function register(): void
    {
        $this->container->bind('def.service', DefService::class);
    }

    public function boot(): void
    {
        self::$booted = true;
    }

    public function provides(): array
    {
        return ['def.service'];
    }

    public function isDeferred(): bool
    {
        return true;
    }
}

class EagerProvider extends ServiceProvider
{
    public static bool $booted = false;

    public function register(): void
    {
        $this->container->bind('eager.service', DefService::class);
    }

    public function boot(): void
    {
        self::$booted = true;
    }
}

class ServiceProviderTest extends TestCase
{
    private Container $container;

    protected function setUp(): void
    {
        $this->container = new Container();
        DefProvider::$booted  = false;
        EagerProvider::$booted = false;
    }

    public function testBaseProviderDefaults(): void
    {
        $provider = new EagerProvider($this->container);

        $this->assertSame([], $provider->provides());
        $this->assertFalse($provider->isDeferred());
    }

    public function testDeferredProviderBootsOnResolve(): void
    {
        $provider = new DefProvider($this->container);
        $provider->register();

        // Mirrors App::registerProviders() deferred wiring.
        foreach ($provider->provides() as $service) {
            $this->container->resolving(
                $service,
                function (object $object, Container $container) use ($provider): void {
                    $provider->boot();
                }
            );
        }

        $this->assertFalse(DefProvider::$booted, 'Should not boot before the service is resolved.');

        $this->container->resolve('def.service');

        $this->assertTrue(DefProvider::$booted, 'Should boot when the provided service is resolved.');
    }

    public function testEagerProviderBootsImmediately(): void
    {
        $provider = new EagerProvider($this->container);
        $provider->register();
        $provider->boot();

        $this->assertTrue(EagerProvider::$booted);
    }
}
