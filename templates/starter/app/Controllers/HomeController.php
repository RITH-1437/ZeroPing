<?php

namespace App\Controllers;

use App\Core\View\Controller;

/**
 * HomeController — the default landing page for a new ZeroPing application.
 *
 * Demonstrates the two most common patterns:
 *  - returning a rendered view (index)
 *  - returning a plain string / JSON (about)
 */
class HomeController extends Controller
{
    /**
     * Render the welcome page.
     */
    public function index(): string
    {
        return $this->view('welcome', [
            'title'    => config('app.name', 'ZeroPing App'),
            'starter'  => env('STARTER_LABEL', 'Starter'),
            'message'  => 'Your ZeroPing application was created successfully.',
            'version'  => \App\Core\Application\App::VERSION,
            'php'      => PHP_VERSION,
        ]);
    }

    /**
     * A simple about page — returns plain text (no layout).
     */
    public function about(): string
    {
        return 'About ' . config('app.name', 'ZeroPing App');
    }
}
