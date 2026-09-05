<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ParentVideo extends Model
{
    protected $table = 'parent_videos';

    protected $fillable = ['parent_id', 'title', 'video_url', 'thumbnail_path', 'sort_order'];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ParentDog::class, 'parent_id');
    }
}
