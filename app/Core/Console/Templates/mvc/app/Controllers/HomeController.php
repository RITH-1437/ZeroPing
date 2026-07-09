<?php

namespace App\Controllers;

use App\Core\View\Controller;

class HomeController extends Controller
{
    public function index(): string
    {
        return view('home', [
            'title' => 'Home',
        ]);
    }
}
