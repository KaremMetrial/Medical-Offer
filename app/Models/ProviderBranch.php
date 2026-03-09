<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProviderBranch extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider_id',
        'country_id',
        'governorate_id',
        'city_id',
        'name_ar',
        'name_en',
        'address',
        'lat',
        'lng',
        'phone',
        'working_hours_json',
        'is_main',
        'is_active'
    ];

    protected $casts = [
        'provider_id' => 'integer',
        'country_id' => 'integer',
        'governorate_id' => 'integer',
        'city_id' => 'integer',
        'lat' => 'decimal:7',
        'lng' => 'decimal:7',
        'working_hours_json' => 'array',
        'is_main' => 'boolean',
        'is_active' => 'boolean'
    ];

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function governorate()
    {
        return $this->belongsTo(Governorate::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function offers()
    {
        return $this->belongsToMany(Offer::class, 'offer_branches');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_provider')
            ->withPivot('provider_id')
            ->withTimestamps();
    }

    public function providerUsers()
    {
        return $this->users()->wherePivot('provider_id', $this->provider_id);
    }

    // Check if branch has any managing users
    public function hasUsers()
    {
        return $this->users()->exists();
    }

    // Get users who can manage this specific branch
    public function managingUsers()
    {
        return $this->users()->where('branch_id', $this->id);
    }

    // Get full address
    public function getFullAddressAttribute()
    {
        $parts = [$this->address];

        if ($this->city) {
            $parts[] = $this->city->name;
        }

        if ($this->governorate) {
            $parts[] = $this->governorate->name;
        }

        if ($this->country) {
            $parts[] = $this->country->name;
        }

        return implode(', ', array_filter($parts));
    }

    public function getNameAttribute()
    {
        $locale = app()->getLocale();
        return $locale === 'ar' ? ($this->name_ar ?? $this->name_en) : ($this->name_en ?? $this->name_ar);
    }

    // Check if branch is active
    public function isActive()
    {
        return $this->is_active && $this->provider->isActive();
    }
}
