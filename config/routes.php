<?php

use App\Core\Router;
use App\Controllers\AuthController;
use App\Controllers\HomeController;
/*
|--------------------------------------------------------------------------
| Home Routes
|--------------------------------------------------------------------------
*/

Router::get('/', [HomeController::class, 'index'], ['auth']);

Router::get('/about', [HomeController::class, 'about']);

Router::get('/request-test', [HomeController::class, 'requestTest']);

Router::get('/session', [HomeController::class, 'session']);

Router::get('/dashboard', [HomeController::class, 'dashboard'], ['auth']);

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

Router::get('/login', [AuthController::class, 'login'], ['guest']);

Router::post('/login', [AuthController::class, 'authenticate'], ['guest']);

Router::get('/register', [AuthController::class, 'register'], ['guest']);

Router::post('/register', [AuthController::class, 'store'], ['guest']);

Router::get('/logout', [AuthController::class, 'logout'], ['auth']);

/*
|--------------------------------------------------------------------------
| User Routes
|--------------------------------------------------------------------------
*/

Router::get('/users', [AuthController::class, 'users'], ['auth']);