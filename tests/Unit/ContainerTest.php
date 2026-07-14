<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\Container\Container;
use PHPUnit\Framework\TestCase;

interface RepoInterface
{
    public function id(): int;
}

class Repo implements RepoInterface
{
    public function id(): int
    {
        return 1;
    }
}

class RepoAlt implements RepoInterface
{
    public function id(): int
    {
        return 2;
    }
}

interface LoggerInterface
{
}

class FileLogger implements LoggerInterface
{
}

class DbLogger implements LoggerInterface
{
}

class Handler
{
    public function __construct(public LoggerInterface $log)
    {
    }
}

class AutoA
{
}

class AutoB
{
    public function __construct(public AutoA $a)
    {
    }
}

interface DiscInterface
{
}

class Disc implements DiscInterface
{
}

class ContainerTest extends TestCase
{
    private Container $container;

    protected function setUp(): void
    {
        $this->container = new Container();
    }

    public function testBindAndResolveInterface(): void
    {
        $this->container->bind(RepoInterface::class, Repo::class);

        $repo = $this->container->resolve(RepoInterface::class);

        $this->assertInstanceOf(Repo::class, $repo);
        $this->assertSame(1, $repo->id());
    }

    public function testMakeAliasesResolve(): void
    {
        $this->container->singleton(RepoInterface::class, Repo::class);

        $this->assertSame(
            $this->container->make(RepoInterface::class),
            $this->container->resolve(RepoInterface::class)
        );
    }

    public function testSingletonReturnsSameInstance(): void
    {
        $this->container->singleton(RepoInterface::class, Repo::class);

        $this->assertSame(
            $this->container->resolve(RepoInterface::class),
            $this->container->resolve(RepoInterface::class)
        );
    }

    public function testInstanceReturnsGivenObject(): void
    {
        $instance = new Repo();
        $this->container->instance(RepoInterface::class, $instance);

        $this->assertSame($instance, $this->container->resolve(RepoInterface::class));
    }

    public function testAutoConstructorInjection(): void
    {
        $b = $this->container->resolve(AutoB::class);

        $this->assertInstanceOf(AutoB::class, $b);
        $this->assertInstanceOf(AutoA::class, $b->a);
    }

    public function testContextualBinding(): void
    {
        $this->container->bind(LoggerInterface::class, FileLogger::class);
        $this->container->when(Handler::class)
            ->needs(LoggerInterface::class)
            ->give(DbLogger::class);

        $handler = $this->container->resolve(Handler::class);

        $this->assertInstanceOf(DbLogger::class, $handler->log);
    }

    public function testContextualBindingWithClosure(): void
    {
        $this->container->when(Handler::class)
            ->needs(LoggerInterface::class)
            ->give(fn () => new DbLogger());

        $handler = $this->container->resolve(Handler::class);

        $this->assertInstanceOf(DbLogger::class, $handler->log);
    }

    public function testInterfaceAutoDiscovery(): void
    {
        $disc = $this->container->resolve(DiscInterface::class);

        $this->assertInstanceOf(Disc::class, $disc);
    }
}
