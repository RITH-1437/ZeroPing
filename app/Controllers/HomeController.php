<?php

namespace App\Controllers;

use App\Core\Application\App;
use App\Core\Auth\SessionGuard;
use App\Core\View\Controller;
use App\Core\Logging\Logger;
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

    public function logTest(): void
    {
        $logger = App::container()->make(Logger::class);

        $logger->info('ZeroPing Logger Test');
        $logger->warning('Warning Test');
        $logger->error('Error Test');

        echo "Logger works!";
    }
}