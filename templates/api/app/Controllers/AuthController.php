<?php

namespace App\Controllers;

use App\Core\View\Controller;
use App\Http\Response;

class AuthController extends Controller
{
    public function login(): string
    {
        $data = json_decode(file_get_contents('php://input'), true);

        $validator = validator($data, [
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);

        if ($validator->fails()) {
            Response::json(['errors' => $validator->errors()], 422);
        }

        Response::json([
            'message' => 'Login endpoint. Implement your authentication logic here.',
        ]);
    }
}
