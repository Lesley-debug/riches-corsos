<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BlogPost extends Model
{
    protected $fillable = [
        'title', 'slug', 'category', 'cover_image', 'excerpt', 'body', 'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::saving(function (BlogPost $post) {
            if (empty($post->slug)) {
                $post->slug = Str::slug($post->title);
            }
        });
    }

    public function scopePublished($query)
    {
        // Allow forward tolerance so posts published in local timezones (WAT UTC+1, etc.)
        // appear immediately without waiting for UTC server time to catch up
        return $query->whereNotNull('published_at')->where('published_at', '<=', now()->addHours(12));
    }
}
