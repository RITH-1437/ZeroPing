<?php

namespace App\Core\Mail;

use App\Core\Application\App;

class Mail
{
    public static function __callStatic(string $method, array $arguments)
    {
        $manager = App::container()->make(MailManager::class);

        return $manager->$method(...$arguments);
    }
}
