<?php

// require_once __DIR__ . '/../core/Controller.php';
// require_once __DIR__ . '/../core/Request.php';

class HomeController extends Controller
{
    public function index()
    {
        $this->view('home/index');
    }

    public function about()
    {
        $this->view('home/about');
    }

    public function requestTest()
    {
        echo Request::method();
    }

    public function session()
    {
        Session::set('project', 'ZeroPing');

        echo Session::get('project');
    }

    public function dashboard(): void
    {
        echo "<h1>Dashboard</h1>";
    }
}