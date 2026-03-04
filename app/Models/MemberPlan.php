<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemberPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'price',
        'duration_days',
        'features_json',
        'is_active',
        'is_provider'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'duration_days' => 'integer',
        'features_json' => 'array',
        'is_active' => 'boolean',
        'is_provider' => 'boolean'
    ];

    public function translations()
    {
        return $this->hasMany(PlanTranslation::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function offerPlanDiscounts()
    {
        return $this->hasMany(OfferPlanDiscount::class);
    }

    // Helper method to get translation
    public function translation($locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        return $this->translations()->where('local', $locale)->first();
    }

    public function getNameAttribute()
    {
        return $this->translation()?->name;
    }

    public function getLabelAttribute()
    {
        return $this->translation()?->label;
    }

    // Check if plan has specific feature
    public function hasFeature($feature)
    {
        return in_array($feature, $this->features_json ?? []);
    }
}
