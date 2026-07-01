<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Session;

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
        Session::set('project', 'ZeroPing');

        echo Session::get('project');
    }

    public function dashboard(): void
    {
        echo "<h1>Dashboard</h1>";
    }
}