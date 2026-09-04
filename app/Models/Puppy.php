<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Puppy extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'breed', 'date_of_birth', 'sex',
        'price', 'status', 'description', 'meta_title', 'meta_description',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'price' => 'decimal:2',
    ];

    // Exposes age_in_weeks in every JSON/Inertia response automatically,
    // so the frontend never has to compute it itself.
    protected $appends = ['age_in_weeks'];

    public function getAgeInWeeksAttribute(): int
    {
        return $this->ageInWeeks();
    }

    protected static function booted(): void
    {
        static::saving(function (Puppy $puppy) {
            if (empty($puppy->slug)) {
                $puppy->slug = Str::slug($puppy->name).'-'.Str::random(4);
            }
        });
    }

    public function images(): HasMany
    {
        return $this->hasMany(PuppyImage::class)->orderBy('sort_order');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function ageInWeeks(): int
    {
        return (int) $this->date_of_birth->diffInWeeks(now());
    }
}
