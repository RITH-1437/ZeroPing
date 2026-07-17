<?php

use App\Core\Routing\Router;

Router::get('/', [\App\Core\Console\WelcomeController::class, 'index']);
