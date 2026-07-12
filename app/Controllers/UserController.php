<?php

namespace App\Controllers;

use App\Core\View\Controller;

class UserController extends Controller
{
    public function index(): void
    {
        $this->view('home/index');
    }

    public function show(string $id): void
    {
        echo "User ID : " . htmlspecialchars($id, ENT_QUOTES, 'UTF-8');
    }
}
