<?php

namespace App\Controllers;

use App\Core\View\Controller;
use App\Http\Response;

class HomeController extends Controller
{
    /**
     * API root — returns service metadata and available endpoints.
     */
    public function index(): void
    {
        Response::json([
            'name' => config('app.name'),
            'framework' => 'ZeroPing',
            'version' => \App\Core\Application\App::VERSION,
            'status' => 'ok',
            'environment' => config('app.env', 'local'),
            'endpoints' => [
                'POST /api/login',
                'GET /api/users',
                'GET /api/users/{id}',
            ],
        ]);
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
