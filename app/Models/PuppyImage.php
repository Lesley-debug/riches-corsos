<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PuppyImage extends Model
{
    use HasFactory;

    protected $fillable = ['puppy_id', 'path', 'alt_text', 'sort_order', 'is_featured'];

    protected $casts = ['is_featured' => 'boolean'];

    public function puppy(): BelongsTo
    {
        return $this->belongsTo(Puppy::class);
    }
}
