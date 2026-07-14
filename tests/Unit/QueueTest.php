<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\Application\App;
use App\Core\Container\Container;
use App\Core\Queue\Dispatcher;
use App\Core\Queue\Job;
use App\Core\Queue\QueueManager;
use App\Core\Queue\Worker;
use PHPUnit\Framework\TestCase;

class QueueTest extends TestCase
{
    protected function setUp(): void
    {
        App::setContainer(new Container());
    }

    public function testSyncDispatchRunsJobImmediately(): void
    {
        $job = new class extends Job {
            public static bool $ran = false;

            public function handle(): void
            {
                self::$ran = true;
            }
        };

        Dispatcher::dispatchSync($job);

        $this->assertTrue($job::$ran);
    }

    public function testArrayDriverPushPopRoundTrip(): void
    {
        $manager = new QueueManager();
        $job = new class extends Job {
            public function handle(): void
            {
                //
            }
        };

        $manager->connection('array')->push($job, 'emails');
        $popped = $manager->connection('array')->pop('emails');

        $this->assertInstanceOf(Job::class, $popped);
        $this->assertSame($job, $popped);
    }

    public function testWorkerProcessesPoppedJob(): void
    {
        $manager = new QueueManager();
        $job = new class extends Job {
            public static bool $ran = false;

            public function handle(): void
            {
                self::$ran = true;
            }
        };

        $manager->connection('array')->push($job, 'default');

        $worker = new Worker($manager);
        $ref = new \ReflectionMethod($worker, 'process');
        $ref->invoke($worker, $job, 1);

        $this->assertTrue($job::$ran);
    }
}
