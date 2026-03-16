<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Story extends Model
{
    protected static function booted()
    {
        static::addGlobalScope(new \App\Scopes\StoryCountryScope());
    }

    protected $fillable = [
        'provider_id',
        'story_type',
        'external_link',
        'media_url',
        'expiry_time',
    ];

    protected $casts = [
        'expiry_time' => 'datetime',
    ];

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    public function views(): HasMany
    {
        return $this->hasMany(StoryView::class);
    }

    public function countries(): BelongsToMany
    {
        return $this->belongsToMany(Country::class, 'country_story');
    }

    /**
     * Scope a query to only include active/not expired stories.
     */
    public function scopeActive($query)
    {
        return $query->where('expiry_time', '>', now());
    }

    public function getImagePathAttribute(): ?string
    {
        return $this->media_url;
    }

    public function getSrcAttribute(): ?string
    {
        return $this->media_url ? asset('storage/' . $this->media_url) : null;
    }
    public function isViewed(): bool
    {
        if ($this->relationLoaded('views')) {
            return $this->views->isNotEmpty();
        }

        $user = auth('sanctum')->user();
        $ip = request()->header('Ip-Device');

        return $this->views()
            ->when($user, function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->when(!$user && $ip, function ($query) use ($ip) {
                $query->where('ip_device', $ip);
            })
            ->when(!$user && !$ip, function ($query) {
                $query->whereRaw('1 = 0');
            })
            ->exists();
    }
}
