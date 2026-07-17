<?php

namespace App\Controllers;

use App\Core\View\Controller;

class HomeController extends Controller
{
    public function index(): string
    {
        $posts = [];
        try {
            $posts = \App\Models\Post::orderBy('created_at', 'desc')->limit(5)->get();
        } catch (\Exception $e) {
            // Database not configured/migrated yet — show an empty list.
            $posts = [];
        }

        return $this->view('home', [
            'title' => 'Welcome',
            'posts' => $posts,
        ], 'app');
    }
}
