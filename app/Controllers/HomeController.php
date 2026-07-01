<?php

namespace App\Controllers;

use App\Auth\SessionGuard;
use App\Core\View\Controller;
use App\Http\Request;

class HomeController extends Controller
{
    public function index(): void
    {
        $this->view('home/index');
    }

    public function about(): void
    {
        $this->view('home/about');
    }

    public function requestTest(): void
    {
        echo Request::method();
    }

    public function session(): void
    {
        SessionGuard::set('project', 'ZeroPing');

        echo SessionGuard::get('project');
    }

    public function dashboard(): void
    {
        echo "<h1>Dashboard</h1>";
    }
}