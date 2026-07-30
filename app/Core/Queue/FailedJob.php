<?php

declare(strict_types=1);

namespace App\Core\Queue;

use App\Core\Database\Model;

/**
 * Eloquent model for the failed_jobs database table.
 *
 * Records jobs that have exceeded their maximum retry attempts,
 * storing the connection, queue, payload, and exception details
 * for later inspection or retry.
 *
 * Table schema:
 * - id: bigint (primary key)
 * - connection: varchar — the queue connection name
 * - queue: varchar — the queue name
 * - payload: longtext — the serialized job payload
 * - exception: longtext — the stringified exception
 * - failed_at: timestamp — when the job failed
 *
 * @property int $id
 * @property string $connection
 * @property string $queue
 * @property string $payload
 * @property string $exception
 * @property string $failed_at
 */
class FailedJob extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected string $table = 'failed_jobs';

    /**
     * Indicates if the model uses soft deletes.
     *
     * @var bool
     */
    protected bool $hasSoftDeletes = false;
}
