<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    protected $fillable = [
        'phone_code',
        'currency_symbol',
        'currency_name',
        'currency_unit',
        'currency_factor',
        'flag',
        'timezone',
        'is_active',
        'contact_email',
        'contact_phone',
        'contact_whatsapp'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'currency_factor' => 'decimal:4'
    ];

    public function translations()
    {
        return $this->hasMany(CountryTranslation::class);
    }

    public function governorates()
    {
        return $this->hasMany(Governorate::class);
    }

    public function providers()
    {
        return $this->hasMany(Provider::class);
    }

    public function memberPlans()
    {
        return $this->hasMany(MemberPlan::class);
    }

    public function branches()
    {
        return $this->hasMany(ProviderBranch::class);
    }

    public function cities()
    {
        return $this->hasManyThrough(City::class, Governorate::class);
    }

    // Helper method to get translation
    public function translation($locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        
        // If translations are already loaded, use them to avoid N+1 queries
        if ($this->relationLoaded('translations')) {
            return $this->translations->firstWhere('local', $locale) ?? $this->translations->first();
        }
        
        // Fall back to query if not loaded
        return $this->translations()->where('local', $locale)->first()
            ?? $this->translations()->first();
    }

    public function getNameAttribute()
    {
        return $this->translation()?->name ?? "Country #{$this->id}";
    }
    public function stories()
    {
        return $this->belongsToMany(Story::class, 'country_story');
    }
    public function scopeSearchName($query, $search)
    {
        return $query->whereHas('translations', function ($q) use ($search) {
            $q->where('name', 'like', '%' . $search . '%');
        });
    }
    public function getFlagUrlAttribute(): ?string
    {
        return $this->flag ? asset('storage/' . $this->flag) : null;
    }
    public function getImagePathAttribute(): ?string
    {
        return $this->flag;
    }

    public function getSrcAttribute(): ?string
    {
        return $this->flag ? asset('storage/' . $this->flag) : null;
    }
    public function getCurrencyFactorAttribute($value)
    {
        if($value == 0){
            return 1;
        }
        return (float) $value;
    }
}
