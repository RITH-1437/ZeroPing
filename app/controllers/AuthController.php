<?php

class AuthController extends Controller
{
    private AuthService $auth;

    public function __construct()
    {
        $this->auth = new AuthService();
    }

    public function login()
    {
        $this->view('auth/login');
    }

    public function register()
    {
        $this->view('auth/register');
    }

    public function store()
    {
        $result = $this->auth->register($_POST);

        if (!$result) {

            echo "Register Failed";

            return;
        }

        Response::redirect('/login');
    }

    public function authenticate()
    {
        $success = $this->auth->login(
            $_POST['email'],
            $_POST['password']
        );

        if (!$success) {

            echo "Invalid email or password.";

            return;
        }

        Response::redirect('/dashboard');
    }

    public function dashboard()
    {
        $user = Auth::user();

        echo "<h1>Welcome {$user['first_name']}</h1>";

        echo "<p>Role : {$user['role']}</p>";
    }

}