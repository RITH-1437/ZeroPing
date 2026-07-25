<?php

namespace App\Controllers;

use App\Core\View\Controller;

class HomeController extends Controller
{
    public function index(): string
    {
        $migrated = false;
        $postCount = 0;
        $latestPosts = [];
        try {
            $postCount = \App\Models\Post::count();
            $latestPosts = \App\Models\Post::orderBy('created_at', 'desc')->limit(3)->get();
            $migrated = true;
        } catch (\Exception $e) {
            // Database not configured/migrated yet
        }

        return $this->view('home', [
            'title'       => 'Welcome',
            'migrated'    => $migrated,
            'postCount'   => $postCount,
            'latestPosts' => $latestPosts,
        ], 'app');
    }
}
