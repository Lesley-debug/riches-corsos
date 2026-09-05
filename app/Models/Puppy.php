<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Puppy extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'breed', 'date_of_birth', 'sex',
        'price', 'status', 'description',
        'color', 'markings', 'weight', 'expected_adult_weight',
        'temperament', 'energy_level', 'compatibility', 'training_progress',
        'vaccination_status', 'vaccination_notes',
        'dewormed', 'vet_checked', 'vet_check_date',
        'microchipped', 'microchip_number',
        'health_guarantee', 'health_guarantee_notes',
        'available_date', 'deposit_required', 'deposit_amount',
        'featured', 'badges', 'visibility',
        'seo_title', 'meta_description',
    ];

    protected $casts = [
        'date_of_birth'    => 'date',
        'vet_check_date'   => 'date',
        'available_date'   => 'date',
        'price'            => 'decimal:2',
        'deposit_amount'   => 'decimal:2',
        'dewormed'         => 'boolean',
        'vet_checked'      => 'boolean',
        'microchipped'     => 'boolean',
        'health_guarantee' => 'boolean',
        'deposit_required' => 'boolean',
        'featured'         => 'boolean',
        'temperament'      => 'array',
        'compatibility'    => 'array',
        'training_progress'=> 'array',
        'badges'           => 'array',
    ];

    protected $appends = ['age_in_weeks'];

    public function getAgeInWeeksAttribute(): ?int
    {
        return $this->ageInWeeks();
    }

    protected static function booted(): void
    {
        static::saving(function (Puppy $puppy) {
            if (empty($puppy->slug)) {
                $base = Str::slug($puppy->name);
                $slug = $base;
                $i = 2;
                while (
                    static::where('slug', $slug)
                        ->when($puppy->exists, fn ($q) => $q->where('id', '!=', $puppy->id))
                        ->exists()
                ) {
                    $slug = "{$base}-{$i}";
                    $i++;
                }
                $puppy->slug = $slug;
            }
        });
    }

    public function images(): HasMany
    {
        return $this->hasMany(PuppyImage::class)->orderBy('sort_order');
    }

    public function videos(): HasMany
    {
        return $this->hasMany(PuppyVideo::class)->orderBy('sort_order');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(PuppyDocument::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function parents(): BelongsToMany
    {
        return $this->belongsToMany(ParentDog::class, 'puppy_parent', 'puppy_id', 'parent_id')
            ->withPivot('role');
    }

    public function sire(): ?ParentDog
    {
        return $this->parents()->wherePivot('role', 'sire')->first();
    }

    public function dam(): ?ParentDog
    {
        return $this->parents()->wherePivot('role', 'dam')->first();
    }

    public function ageInWeeks(): ?int
    {
        return $this->date_of_birth ? (int) $this->date_of_birth->diffInWeeks(now()) : null;
    }

    public function featuredImage(): ?PuppyImage
    {
        return $this->images()->where('is_featured', true)->first()
            ?? $this->images()->first();
    }
}
