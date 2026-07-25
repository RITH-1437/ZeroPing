<?php

namespace App\Controllers;

use App\Core\View\Controller;

class HomeController extends Controller
{
    public function index(): string
    {
        $migrated = false;
        $userCount = 0;
        try {
            $userCount = \App\Models\User::count();
            $migrated = true;
        } catch (\Exception $e) {
            // Database not migrated yet
        }

        return $this->view('home', [
            'title'     => 'Welcome',
            'migrated'  => $migrated,
            'userCount' => $userCount,
        ], 'app');
    }
}
