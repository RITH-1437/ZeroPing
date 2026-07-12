<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\Container\Container;

class ContainerTest extends \Tests\TestCase
{
    public function testMakeReturnsNewInstanceForUnboundClass(): void
    {
        $container = new Container();

        $result = $container->make(Container::class);

        $this->assertInstanceOf(Container::class, $result);
    }

    public function testBindReturnsNewInstanceEachTime(): void
    {
        $container = new Container();

        $container->bind('counter', fn() => new \stdClass());

        $a = $container->make('counter');
        $b = $container->make('counter');

        $this->assertNotSame($a, $b);
    }

    public function testSingletonReturnsSameInstance(): void
    {
        $container = new Container();

        $container->singleton('config', fn() => (object)['debug' => true]);

        $a = $container->make('config');
        $b = $container->make('config');

        $this->assertSame($a, $b);
    }

    public function testInstanceReturnsProvidedObject(): void
    {
        $container = new Container();
        $obj = new \stdClass();
        $obj->name = 'test';

        $container->instance('myobj', $obj);

        $result = $container->make('myobj');

        $this->assertSame($obj, $result);
        $this->assertSame('test', $result->name);
    }

    public function testInstanceOverridesBinding(): void
    {
        $container = new Container();

        $container->bind('service', fn() => (object)['type' => 'bound']);
        $container->instance('service', (object)['type' => 'instance']);

        $result = $container->make('service');

        $this->assertSame('instance', $result->type);
    }

    public function testMakeResolvesClassConstructorDependencies(): void
    {
        $container = new Container();

        $result = $container->make(\stdClass::class);

        $this->assertInstanceOf(\stdClass::class, $result);
    }

    public function testMakeThrowsForNonExistentClass(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Class NonExistent\\FakeClass not found.');

        $container = new Container();
        $container->make('NonExistent\\FakeClass');
    }

    public function testBindWithStringClassResolvesClass(): void
    {
        $container = new Container();
        $container->bind('std', \stdClass::class);

        $result = $container->make('std');

        $this->assertInstanceOf(\stdClass::class, $result);
    }

    public function testSingletonWithStringClassResolvesAndCaches(): void
    {
        $container = new Container();
        $container->singleton('std', \stdClass::class);

        $a = $container->make('std');
        $b = $container->make('std');

        $this->assertSame($a, $b);
    }

    public function testMakeWithClosureReceivesContainer(): void
    {
        $container = new Container();
        $receivedContainer = null;

        $container->bind('test', function (Container $c) use (&$receivedContainer) {
            $receivedContainer = $c;
            return new \stdClass();
        });

        $container->make('test');

        $this->assertSame($container, $receivedContainer);
    }

    public function testMultipleSingletonsAreIndependent(): void
    {
        $container = new Container();

        $container->singleton('a', fn() => (object)['val' => 1]);
        $container->singleton('b', fn() => (object)['val' => 2]);

        $a = $container->make('a');
        $b = $container->make('b');

        $this->assertNotSame($a, $b);
        $this->assertSame(1, $a->val);
        $this->assertSame(2, $b->val);
    }

    public function testBindClosureCanReturnSharedInstanceManually(): void
    {
        $container = new Container();
        $instance = null;

        $container->bind('manual', function () use (&$instance) {
            if ($instance === null) {
                $instance = new \stdClass();
            }
            return $instance;
        });

        $a = $container->make('manual');
        $b = $container->make('manual');

        $this->assertSame($a, $b);
    }
}
