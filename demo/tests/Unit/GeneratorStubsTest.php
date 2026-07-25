<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\Console\Command;
use App\Core\Console\Generators\Generator;
use App\Core\Console\Generators\MakeEnumCommand;
use App\Core\Console\Generators\MakeEventCommand;
use App\Core\Console\Generators\MakeFactoryCommand;
use App\Core\Console\Generators\MakeJobCommand;
use App\Core\Console\Generators\MakeListenerCommand;
use App\Core\Console\Generators\MakeNotificationCommand;
use PHPUnit\Framework\TestCase;

class GeneratorStubsTest extends TestCase
{
    public function testGeneratorCommandsAreInstantiableAndSubclassCommand(): void
    {
        $classes = [
            MakeJobCommand::class,
            MakeEventCommand::class,
            MakeListenerCommand::class,
            MakeNotificationCommand::class,
            MakeFactoryCommand::class,
            MakeEnumCommand::class,
        ];

        foreach ($classes as $class) {
            $instance = new $class();

            $this->assertInstanceOf(Generator::class, $instance);
            $this->assertInstanceOf(Command::class, $instance);
        }
    }

    public function testJobStubRenders(): void
    {
        $out = $this->render(new MakeJobCommand(), 'job.stub', ['class' => 'SendEmailJob']);

        $this->assertStringContainsString('use App\\Core\\Queue\\Job;', $out);
        $this->assertStringContainsString('class SendEmailJob extends Job', $out);
        $this->assertStringContainsString('public function handle(): void', $out);
    }

    public function testEventStubRenders(): void
    {
        $out = $this->render(new MakeEventCommand(), 'event.stub', ['class' => 'OrderShipped']);

        $this->assertStringContainsString('use App\\Core\\Events\\Event;', $out);
        $this->assertStringContainsString('class OrderShipped extends Event', $out);
    }

    public function testListenerStubBindsEvent(): void
    {
        $out = $this->render(new MakeListenerCommand(), 'listener.stub', [
            'class'         => 'LogOrderShipped',
            'event_class'   => 'OrderShippedEvent',
            'event_import'  => 'use App\\Events\\OrderShippedEvent;',
        ]);

        $this->assertStringContainsString('use App\\Events\\OrderShippedEvent;', $out);
        $this->assertStringContainsString('class LogOrderShipped implements Listener', $out);
        $this->assertStringContainsString('handle(OrderShippedEvent $event): void', $out);
    }

    public function testNotificationStubRenders(): void
    {
        $out = $this->render(new MakeNotificationCommand(), 'notification.stub', ['class' => 'InvoicePaid']);

        $this->assertStringContainsString('class InvoicePaid', $out);
        $this->assertStringContainsString('extends Notification', $out);
        $this->assertStringContainsString('public function via(object $notifiable): array', $out);
        $this->assertStringContainsString('public function toMail(object $notifiable): Mailable', $out);
    }

    public function testFactoryStubBindsModel(): void
    {
        $out = $this->render(new MakeFactoryCommand(), 'factory.stub', [
            'class'         => 'PostFactory',
            'model_import'  => 'use App\\Models\\Post;',
            'model_prop'    => '    protected string $model = Post::class;',
        ]);

        $this->assertStringContainsString('use App\\Models\\Post;', $out);
        $this->assertStringContainsString('protected string $model = Post::class;', $out);
        $this->assertStringContainsString('public function definition(): array', $out);
    }

    public function testEnumStubRenders(): void
    {
        $out = $this->render(new MakeEnumCommand(), 'enum.stub', ['class' => 'Status']);

        $this->assertStringContainsString('enum Status: string', $out);
        $this->assertStringContainsString("case Active = 'active';", $out);
    }

    private function render(Command $command, string $stub, array $data): string
    {
        $stubMethod = new \ReflectionMethod($command, 'stub');
        $replaceMethod = new \ReflectionMethod($command, 'replace');

        return (string) $replaceMethod->invoke(
            $command,
            $stubMethod->invoke($command, $stub),
            $data
        );
    }
}
