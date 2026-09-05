<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ParentImage extends Model
{
    protected $table = 'parent_images';

    protected $fillable = ['parent_id', 'path', 'alt_text', 'sort_order', 'is_primary'];

    protected $casts = ['is_primary' => 'boolean'];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ParentDog::class, 'parent_id');
    }
}
