<?php

namespace App\Controllers;

use App\Core\View\Controller;

class HomeController extends Controller
{
    public function index(): string
    {
        return $this->view('welcome', [
            'title' => 'Welcome',
        ], 'app');
    }
}
