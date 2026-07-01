<?php

use App\Core\Router;

use App\Controllers\AuthController;
use App\Controllers\HomeController;
use App\Controllers\UserController;
use App\Controllers\ProfileController;
use App\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Router::get('/about', [HomeController::class, 'about']);
Router::get('/session', [HomeController::class, 'session']);

/*
|--------------------------------------------------------------------------
| Guest Routes
|--------------------------------------------------------------------------
*/

Router::middleware(['guest'], function () {

    Router::get('/login', [AuthController::class, 'login']);
    Router::post('/login', [AuthController::class, 'authenticate']);

    Router::get('/register', [AuthController::class, 'register']);
    Router::post('/register', [AuthController::class, 'store']);

});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Router::middleware(['auth'], function () {

    Router::get('/', [HomeController::class, 'index']);

    Router::get('/dashboard', [HomeController::class, 'dashboard']);

    Router::get('/logout', [AuthController::class, 'logout']);

    Router::get('/users', [UserController::class, 'index']);

    Router::get('/users/{id}', [UserController::class, 'show']);

    Router::get('/profile', [ProfileController::class, 'index']);

});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Router::prefix('/admin', function () {

    Router::middleware(['auth'], function () {

        Router::get('/dashboard', [DashboardController::class, 'index']);

        Router::get('/users', [UserController::class, 'index']);

    });

});