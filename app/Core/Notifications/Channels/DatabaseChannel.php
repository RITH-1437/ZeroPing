<?php

namespace App\Core\Notifications\Channels;

use App\Core\Database\Database;
use App\Core\Notifications\Notification;
use App\Core\Support\Log;

/**
 * Persists a notification to the "notifications" table.
 *
 * The table is expected to exist (ship a migration that creates it). If it is
 * missing, the payload is silently logged instead of throwing.
 */
class DatabaseChannel implements Channel
{
    public function send(object $notifiable, Notification $notification, mixed $payload): void
    {
        if (!is_array($payload)) {
            return;
        }

        $type = $payload['type'] ?? $notification::class;
        $data = $payload['data'] ?? $payload;

        try {
            $connection = Database::connection();

            $connection->statement(
                'INSERT INTO notifications (type, notifiable_type, notifiable_id, data, created_at) '
                . 'VALUES (?, ?, ?, ?, ?)',
                [
                    $type,
                    $payload['notifiable_type'] ?? get_class($notifiable),
                    $payload['notifiable_id'] ?? ($notifiable->id ?? 0),
                    (string) json_encode($data),
                    date('Y-m-d H:i:s'),
                ]
            );
        } catch (\Throwable $e) {
            Log::error('DatabaseChannel failed: ' . $e->getMessage());
        }
    }
}
