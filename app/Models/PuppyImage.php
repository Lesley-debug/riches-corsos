<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PuppyImage extends Model
{
    use HasFactory;

    protected $fillable = ['puppy_id', 'path', 'sort_order'];

    public function puppy(): BelongsTo
    {
        return $this->belongsTo(Puppy::class);
    }
}
