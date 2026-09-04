<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    protected $fillable = ['author_name', 'rating', 'content', 'is_featured'];

    protected $casts = [
        'is_featured' => 'boolean',
    ];
}
