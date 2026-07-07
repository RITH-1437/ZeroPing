<?php

namespace App\Core\Database;

use PDOStatement;

class ProfiledStatement extends PDOStatement
{
    protected \PDO $db;

    protected function __construct(\PDO $db)
    {
        $this->db = $db;
    }

    public function execute($bindings = null): bool
    {
        $start = microtime(true);
        $result = parent::execute($bindings);
        $time = microtime(true) - $start;

        Database::addLog($this->queryString, $bindings, $time);

        return $result;
    }
}
