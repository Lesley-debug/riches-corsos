<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'puppy_id', 'buyer_name', 'buyer_email', 'buyer_phone',
        'buyer_address', 'notes', 'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function puppy(): BelongsTo
    {
        return $this->belongsTo(Puppy::class);
    }
}
