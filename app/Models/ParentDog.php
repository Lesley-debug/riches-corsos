<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ParentDog extends Model
{
    protected $table = 'parents';

    protected $fillable = [
        'name', 'slug', 'parent_type', 'breed', 'date_of_birth',
        'description', 'color', 'weight', 'height',
        'registration_organization', 'registration_number',
        'health_tests', 'health_notes', 'titles',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'health_tests'  => 'array',
        'titles'        => 'array',
    ];

    protected static function booted(): void
    {
        static::saving(function (ParentDog $parent) {
            if (empty($parent->slug)) {
                $base = Str::slug($parent->name);
                $slug = $base;
                $i = 2;
                while (
                    static::where('slug', $slug)
                        ->when($parent->exists, fn ($q) => $q->where('id', '!=', $parent->id))
                        ->exists()
                ) {
                    $slug = "{$base}-{$i}";
                    $i++;
                }
                $parent->slug = $slug;
            }
        });
    }

    public function images(): HasMany
    {
        return $this->hasMany(ParentImage::class, 'parent_id')->orderBy('sort_order');
    }

    public function videos(): HasMany
    {
        return $this->hasMany(ParentVideo::class, 'parent_id')->orderBy('sort_order');
    }

    public function puppies(): BelongsToMany
    {
        return $this->belongsToMany(Puppy::class, 'puppy_parent', 'parent_id', 'puppy_id')
            ->withPivot('role');
    }

    public function primaryImage(): ?ParentImage
    {
        return $this->images()->where('is_primary', true)->first()
            ?? $this->images()->first();
    }
}
