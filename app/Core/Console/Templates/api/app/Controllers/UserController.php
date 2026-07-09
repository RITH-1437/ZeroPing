<?php

namespace App\Controllers;

use App\Core\View\Controller;

class UserController extends Controller
{
    public function index(): string
    {
        return $this->json([
            'users' => [
                ['id' => 1, 'name' => 'Alice'],
                ['id' => 2, 'name' => 'Bob'],
            ],
        ]);
    }

    public function show(int $id): string
    {
        return $this->json([
            'user' => [
                'id' => $id,
                'name' => 'User #' . $id,
            ],
        ]);
    }
}
