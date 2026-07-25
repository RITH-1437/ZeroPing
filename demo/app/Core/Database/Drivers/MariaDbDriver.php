<?php

namespace App\Core\Database\Drivers;

/**
 * MariaDB driver.
 *
 * MariaDB is wire-compatible with MySQL, so it reuses the MySQL driver and
 * grammar. It is exposed as its own connection name so operators can make
 * the distinction explicit in configuration.
 */
class MariaDbDriver extends MySqlDriver
{
    public function getName(): string
    {
        return 'mariadb';
    }
}
