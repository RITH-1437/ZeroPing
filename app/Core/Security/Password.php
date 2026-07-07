<?php

namespace App\Core\Security;

class Password
{
    public static function broker(): PasswordBroker
    {
        // This is a simplified implementation. A real implementation would
        // use a password broker manager.
        return new PasswordBroker();
    }
}
