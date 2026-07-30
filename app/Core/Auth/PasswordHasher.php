<?php

declare(strict_types=1);

namespace App\Core\Auth;

use App\Core\Security\Hash;

/**
 * Password hashing utility for the Auth subsystem.
 *
 * Extends the core Hash class to provide a semantically clear entry point
 * for password hashing operations within the authentication layer.
 *
 * @see \App\Core\Security\Hash
 */
class PasswordHasher extends Hash
{
}
