<?php

declare(strict_types=1);

namespace App\Core\Console\Generators;

class MakeNotificationCommand extends Generator
{
    protected string $description = 'Create a new notification class';

    public function handle(string $name): void
    {
        if (empty($name)) {
            $this->warn('Usage: php zero make:notification NotificationName');

            return;
        }

        $name = $this->ensureSuffix($name, 'Notification');

        $content = $this->replace($this->stub('notification.stub'), [
            'class' => $name,
        ]);

        $file = BASE_PATH . "/app/Notifications/{$name}.php";

        $this->writeGenerated($file, $content, 'Notification');
    }
}
