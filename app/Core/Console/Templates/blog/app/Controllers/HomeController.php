<?php

namespace App\Controllers;

use App\Core\View\Controller;

class HomeController extends Controller
{
    public function index(): string
    {
        $posts = \App\Models\Post::latest()->get();

        return view('home', [
            'title' => 'Home',
            'posts' => $posts,
        ]);
    }
}
