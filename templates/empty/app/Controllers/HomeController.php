<?php

namespace App\Controllers;

use App\Core\View\Controller;

class HomeController extends Controller
{
    public function index(): string
    {
        return $this->view('welcome', [
            'title'   => config('app.name', 'ZeroPing App'),
            'version' => \App\Core\Application\App::VERSION,
            'php'     => PHP_VERSION,
        ], null);
    }
}
