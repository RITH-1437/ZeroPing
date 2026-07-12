<?php

namespace App\Controllers;

use App\Core\View\Controller;
use App\Models\User;

class DashboardController extends Controller
{
    public function index(): string
    {
        $totalUsers = User::count();
        $recentUsers = User::orderBy('created_at', 'desc')->limit(5)->get();

        return view('dashboard', [
            'title' => 'Dashboard',
            'totalUsers' => $totalUsers,
            'recentUsers' => $recentUsers,
        ]);
    }
}
