<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PuppyVideo extends Model
{
    protected $fillable = ['puppy_id', 'title', 'video_url', 'file_path', 'source', 'thumbnail_path', 'sort_order'];

    public function puppy(): BelongsTo
    {
        return $this->belongsTo(Puppy::class);
    }
}
