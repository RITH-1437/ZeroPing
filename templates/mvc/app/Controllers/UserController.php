<?php

namespace App\Controllers;

use App\Core\View\Controller;
use App\Models\User;

class UserController extends Controller
{
    public function index(): string
    {
        $users = User::all();

        return $this->view('users.index', [
            'title' => 'Users',
            'users' => $users,
        ], 'app');
    }

    public function create(): string
    {
        return $this->view('users.create', [
            'title' => 'Create User',
        ], 'app');
    }

    public function store(): string
    {
        $data = request()->all();

        $validator = validator($data, [
            'name' => 'required|min:2',
            'email' => 'required|email|unique:users,email',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        }

        User::create($data);

        return redirect('/users')->with('success', 'User created!');
    }
}
