<?php

namespace App\Controllers;

use App\Core\View\Controller;
use App\Http\Response;

class HomeController extends Controller
{
    /**
     * API landing page — renders a browsable welcome page with endpoint docs.
     */
    public function index(): string
    {
        return $this->view('welcome', [
            'title'   => config('app.name', 'API'),
            'version' => \App\Core\Application\App::VERSION,
            'php'     => PHP_VERSION,
            'env'     => config('app.env', 'local'),
        ], null);
    }

    /**
     * Simple health check endpoint.
     */
    public function health(): void
    {
        Response::json([
            'status' => 'healthy',
            'timestamp' => date('c'),
        ]);
    }
}
