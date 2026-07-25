<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\Container\Container;
use App\Core\Events\EventDispatcher;
use App\Core\Events\Event;
use App\Core\Events\Listener;
use App\Core\Mail\MailManager;
use App\Core\Mail\Mailable;
use App\Core\Notifications\Channels\Channel;
use App\Core\Notifications\Notification;
use App\Core\Notifications\NotificationManager;
use App\Core\Notifications\Channels\LogChannel;
use App\Core\Notifications\Channels\MailChannel;
use App\Core\Notifications\Notifiable;
use App\Core\Scheduling\Schedule;
use App\Core\Scheduling\ScheduleManager;
use App\Core\Support\Config;
use App\Providers\ServiceProvider as FrameworkServiceProvider;
use PHPUnit\Framework\TestCase;
use Zeroping\Support\ServiceProvider as PackageServiceProvider;

class NotificationTest extends TestCase
{
    private string $logFile;

    protected function setUp(): void
    {
        $this->logFile = BASE_PATH . '/storage/logs/zeroping.log';
        Config::set('mail.default', 'array');
    }

    private function mailManager(): MailManager
    {
        return new MailManager();
    }

    public function testMailChannelReceivesMailable(): void
    {
        $mail = $this->mailManager();
        $container = new Container();
        $container->singleton(MailManager::class, fn () => $mail);

        $manager = new NotificationManager($container);
        $manager->channel('mail', new MailChannel($mail));

        $notification = new class extends Notification {
            public function via(object $n): array
            {
                return ['mail'];
            }

            public function toMail(object $n): mixed
            {
                return (new class extends Mailable {
                })->subject('Hi')->view('welcome');
            }
        };

        $manager->send(new \stdClass(), $notification);

        $this->assertCount(1, $mail->mailer('array')->getDriver()->sentMessages());
    }

    public function testLogChannelWritesPayload(): void
    {
        $container = new Container();
        $manager = new NotificationManager($container);
        $manager->channel('log', new LogChannel());

        $notification = new class extends Notification {
            public function via(object $n): array
            {
                return ['log'];
            }

            public function toArray(object $n): array
            {
                return ['msg' => 'hello'];
            }
        };

        $manager->send(new \stdClass(), $notification);

        $log = file_get_contents($this->logFile);
        $this->assertStringContainsString('[notification]', $log);
        $this->assertStringContainsString('hello', $log);
    }

    public function testManagerRoutesToDeclaredChannels(): void
    {
        $spy = new class implements Channel {
            public array $calls = [];

            public function send(object $notifiable, Notification $notification, mixed $payload): void
            {
                $this->calls[] = $payload;
            }
        };

        $container = new Container();
        $manager = new NotificationManager($container);
        $manager->channel('spy', $spy);

        $notification = new class extends Notification {
            public function via(object $n): array
            {
                return ['spy'];
            }

            public function toSpy(object $n): mixed
            {
                return 'payload';
            }
        };

        $manager->send(new \stdClass(), $notification);

        $this->assertSame(['payload'], $spy->calls);
    }

    public function testNotifiableTraitDispatchesViaFacade(): void
    {
        $container = new Container();
        $container->singleton(
            NotificationManager::class,
            function (Container $c) {
                $manager = new NotificationManager($c);
                $manager->channel('log', new LogChannel());

                return $manager;
            }
        );
        \App\Core\Application\App::setContainer($container);

        $notifiable = new class {
            use Notifiable;
        };

        $notification = new class extends Notification {
            public function via(object $n): array
            {
                return ['log'];
            }

            public function toArray(object $n): array
            {
                return ['via' => 'trait'];
            }
        };

        $notifiable->notify($notification);

        $this->assertStringContainsString(
            'via":"trait',
            (string) file_get_contents($this->logFile)
        );
    }

    public function testPackageProviderChannelsAggregate(): void
    {
        $provider = new class (new Container()) extends PackageServiceProvider {
            public function register(): void
            {
                $this->channels([
                    'sms' => LogChannel::class,
                ]);
            }
        };

        $provider->register();

        $this->assertArrayHasKey('sms', PackageServiceProvider::allChannels());
    }

    public function testFrameworkProviderHookDefaults(): void
    {
        $provider = new class (new Container()) extends FrameworkServiceProvider {
            public function register(): void
            {
            }
        };

        $this->assertSame([], $provider->listens());

        $schedule = new Schedule();
        $provider->schedules($schedule);
        $this->assertCount(0, $schedule->events());
    }

    public function testPackageScheduleContributesToScheduler(): void
    {
        $schedule = (new ScheduleManager())->schedule();

        $provider = new class (new Container()) extends FrameworkServiceProvider {
            public function register(): void
            {
            }

            public function schedules(Schedule $s): void
            {
                $s->command('demo:heartbeat');
            }
        };

        $provider->schedules($schedule);

        $this->assertCount(1, $schedule->events());
    }

    public function testEventBusDispatchesToListener(): void
    {
        $event = new class extends Event {
        };

        $listener = new class implements Listener {
            public static bool $fired = false;

            public function handle($e): void
            {
                self::$fired = true;
            }
        };

        $dispatcher = new EventDispatcher();
        $dispatcher->listen(get_class($event), get_class($listener));
        $dispatcher->dispatch($event);

        $this->assertTrue($listener::$fired);
    }
}
