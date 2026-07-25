<?php

namespace App\Core\Queue;

use App\Core\Application\App;

class Dispatcher
{
    public static function dispatch(Job $job): void
    {
        App::container()->make(QueueManager::class)->connection($job->queue)->push($job);
    }

    public static function dispatchSync(Job $job): void
    {
        App::container()->make(QueueManager::class)->connection('sync')->push($job);
    }

    public static function dispatchLater(int $delay, Job $job): void
    {
        App::container()->make(QueueManager::class)->connection($job->queue)->later($delay, $job);
    }
}
