<?php

namespace App\Controllers;

use App\Core\View\Controller;
use App\Models\User;

class DashboardController extends Controller
{
    public function index(): string
    {
        $totalUsers = 0;
        $recentUsers = [];

        try {
            $totalUsers = User::count();
            $recentUsers = User::orderBy('created_at', 'desc')->limit(5)->get();
        } catch (\Exception $e) {
            // Database not configured/migrated yet — show empty state.
        }

        return $this->view('dashboard', [
            'title' => 'Dashboard',
            'totalUsers' => $totalUsers,
            'recentUsers' => $recentUsers,
        ], 'app');
    }
}
