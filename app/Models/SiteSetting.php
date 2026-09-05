<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    public const CACHE_KEY = 'site_settings.current';

    protected $fillable = [
        'hero_image',
        'company_name',
        'tagline',
        'phone',
        'email',
        'website',
        'address',
        'whatsapp',
        'facebook',
        'instagram',
        'tiktok',
        'representative_name',
        'representative_title',
        'signature_image',
    ];

    public static function current(): self
    {
        $id = Cache::rememberForever(self::CACHE_KEY, fn () => static::firstOrCreate([])->id);

        return static::find($id) ?? static::firstOrCreate([]);
    }
}
