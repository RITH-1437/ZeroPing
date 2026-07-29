<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View\Controller;
use App\Http\Response;

class UserController extends Controller
{
    public function index(): void
    {
        Response::json([
            'users' => [
                ['id' => 1, 'name' => 'Alice'],
                ['id' => 2, 'name' => 'Bob'],
            ],
        ]);
    }

    public function show(int $id): void
    {
        Response::json([
            'user' => [
                'id' => $id,
                'name' => 'User #' . $id,
            ],
        ]);
    }
}
