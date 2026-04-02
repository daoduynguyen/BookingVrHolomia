<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'address',
        'hotline',
        'description',
        'banner_image',
        'opening_hours',
        'maps_url',
        'facebook_url',
        'is_active',
        'color',
        'logo_url',
        'banner_url',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(function (Location $location) {
            if (empty($location->slug)) {
                $location->slug = Str::slug($location->name);
            }
        });
    }

    // -------------------------------------------------------
    // Relationships
    // -------------------------------------------------------

    /** Một cơ sở có nhiều Ticket (nhiều-nhiều) */
    public function tickets()
    {
        return $this->belongsToMany(Ticket::class);
    }

    // -------------------------------------------------------
    // Helpers
    // -------------------------------------------------------

    /** URL landing page của cơ sở */
   public function landingUrl(): string
{
    $domain = env('APP_DOMAIN', 'holomia.test');
    return 'https://' . $this->slug . '.' . $domain;
}
    /** Scope: chỉ lấy cơ sở đang hoạt động */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
