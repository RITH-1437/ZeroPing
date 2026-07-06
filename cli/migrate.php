<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/config.php';

use App\Core\Console\Commands\MigrateCommand;

$command = new MigrateCommand();

$command->handle();