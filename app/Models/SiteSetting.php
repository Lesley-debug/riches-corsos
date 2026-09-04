<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    public const CACHE_KEY = 'site_settings.current';

    protected $fillable = ['hero_image'];

    public static function current(): self
    {
        return Cache::rememberForever(self::CACHE_KEY, fn () => static::firstOrCreate([]));
    }
}
