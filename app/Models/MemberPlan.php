<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemberPlan extends Model
{
    use HasFactory, \App\Traits\BelongsToCountry;

    protected $fillable = [
        'price',
        'duration_days',
        'features_json',
        'is_active',
        'is_provider',
        'country_id',
    ];

    protected $casts = [
        'price' => 'decimal:6',
        'duration_days' => 'integer',
        'features_json' => 'array',
        'is_active' => 'boolean',
        'is_provider' => 'boolean',
        'country_id' => 'integer',
    ];

    public function translations()
    {
        return $this->hasMany(PlanTranslation::class, 'plan_id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function offerPlanDiscounts()
    {
        return $this->hasMany(OfferPlanDiscount::class);
    }

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
        return $this->translation()?->name;
    }

    public function getFeatureAttribute()
    {
        return $this->translation()?->feature;
    }
    public function getLabelAttribute()
    {
        return $this->translation()?->label;
    }

    // Check if plan has specific feature
    public function hasFeature($feature)
    {
        if (is_null($this->features_json)) {
            return false;
        }

        // Check if it's a key (for key-value pairs)
        if (array_key_exists($feature, $this->features_json)) {
            return true;
        }

        // Check if it's a value (for simple lists)
        return in_array($feature, $this->features_json);
    }
    public function scopeSearchName($query, $search)
    {
        return $query->whereHas('translations', function ($q) use ($search) {
            $q->where('name', 'like', '%' . $search . '%');
        });
    }
}
