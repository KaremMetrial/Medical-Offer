<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider_id',
        'category_id',
        'discount_percent',
        'start_date',
        'end_date',
        'status',
        'show_in_home',
        'sort_order',
        'views'
    ];

    protected $casts = [
        'provider_id' => 'integer',
        'category_id' => 'integer',
        'discount_percent' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
        'show_in_home' => 'boolean',
        'sort_order' => 'integer',
        'views' => 'integer'
    ];

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function translations()
    {
        return $this->hasMany(OfferTranslation::class);
    }

    public function images()
    {
        return $this->hasMany(OfferImage::class);
    }

    public function branches()
    {
        return $this->belongsToMany(ProviderBranch::class, 'offer_branches');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'offer_categories');
    }

    public function planDiscounts()
    {
        return $this->hasMany(OfferPlanDiscount::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function offerViews()
    {
        return $this->hasMany(OfferView::class);
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

    public function getDescriptionAttribute()
    {
        return $this->translation()?->description;
    }

    public function getTermsAttribute()
    {
        return $this->translation()?->terms;
    }

    // Get main image
    public function getMainImageAttribute()
    {
        return $this->images()->where('type', 'image')->orderBy('sort_order')->first();
    }

    // Check if offer is active
    public function isActive()
    {
        return $this->status === 'published' &&
               $this->start_date <= now() &&
               $this->end_date >= now();
    }

    // Check if offer is expired
    public function isExpired()
    {
        return $this->end_date < now();
    }

    // Get discount for specific plan
    public function getDiscountForPlan($planId)
    {
        $planDiscount = $this->planDiscounts()->where('plan_id', $planId)->first();
        return $planDiscount ? $planDiscount->discount_percent : $this->discount_percent;
    }
}
