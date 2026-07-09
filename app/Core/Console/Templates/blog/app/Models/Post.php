<?php

namespace App\Models;

use App\Core\Model;

class Post extends Model
{
    protected string $table = 'posts';

    protected array $fillable = [
        'title', 'slug', 'content', 'excerpt', 'published_at',
    ];

    protected array $casts = [
        'published_at' => 'datetime',
    ];

    public function scopePublished(self $query): self
    {
        return $query->whereNotNull('published_at')
            ->where('published_at', '<=', date('Y-m-d H:i:s'));
    }
}
