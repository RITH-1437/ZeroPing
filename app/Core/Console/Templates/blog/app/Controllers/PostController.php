<?php

namespace App\Controllers;

use App\Core\View\Controller;
use App\Models\Post;

class PostController extends Controller
{
    public function index(): string
    {
        $posts = Post::latest()->paginate(10);

        return $this->view('posts.index', [
            'title' => 'Blog',
            'posts' => $posts,
        ], 'app');
    }

    public function show(string $slug): string
    {
        $post = Post::where('slug', $slug)->firstOrFail();

        return $this->view('posts.show', [
            'title' => $post->title,
            'post' => $post,
        ], 'app');
    }
}
