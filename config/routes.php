<?php

/*
|--------------------------------------------------------------------------
| Home Routes
|--------------------------------------------------------------------------
*/

Router::get('/', 'HomeController@index', ['auth']);
Router::get('/about', 'HomeController@about');
Router::get('/request-test', 'HomeController@requestTest');
Router::get('/session', 'HomeController@session');
Router::get('/dashboard', 'HomeController@dashboard', ['auth']);

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

Router::get('/login', 'AuthController@login', ['guest']);
Router::post('/login', 'AuthController@authenticate', ['guest']);

Router::get('/register', 'AuthController@register', ['guest']);
Router::post('/register', 'AuthController@store', ['guest']);

/*
|--------------------------------------------------------------------------
| User Routes
|--------------------------------------------------------------------------
*/

Router::get('/users', 'AuthController@users', ['auth']);