<?php

namespace App\Controllers;

use App\Auth\AuthManager;
use App\Core\View\Controller;
use App\Http\Response;
use App\Services\AuthenticationService;
use App\Support\Flash;

class AuthController extends Controller
{
    private AuthenticationService $auth;

    public function __construct()
    {
        $this->auth = new AuthenticationService();
    }

    /**
     * Show login page
     */
    public function login(): void
    {
        $this->view('auth/login');
    }

    /**
     * Show register page
     */
    public function register(): void
    {
        $this->view('auth/register');
    }

    /**
     * Register a new user
     */
    public function store(): void
    {
        $result = $this->auth->register(Request::all());

        if (!$result) {
            Flash::error('Registration failed. Please check your information and try again.');
            Response::redirect('/register');
        }

        Flash::success('Registration completed successfully.');

        Response::redirect('/login');
    }

    /**
     * Login user
     */
    public function authenticate(): void
    {
        $success = $this->auth->login(
            $_POST['email'] ?? '',
            $_POST['password'] ?? ''
        );

        if (!$success) {
            Flash::error('Invalid email or password.');
            Response::redirect('/login');
        }

        Flash::success('Welcome back!');

        Response::redirect('/dashboard');
    }

    /**
     * Logout current user
     */
    public function logout(): void
    {
        AuthManager::logout();

        Flash::success('You have logged out successfully.');

        Response::redirect('/login');
    }

    /**
     * Dashboard page
     */
    public function dashboard(): void
    {
        $user = AuthManager::user();

        if (!$user) {
            Response::redirect('/login');
        }

        echo "<h1>Welcome {$user['first_name']}!</h1>";
        echo "<p>Email : {$user['email']}</p>";
        echo "<p>Role : {$user['role']}</p>";
    }
}