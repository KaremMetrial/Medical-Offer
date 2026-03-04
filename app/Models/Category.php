<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'icon',
        'parent_id',
        'is_show',
        'is_active',
        'sort_order'
    ];

    protected $casts = [
        'parent_id' => 'integer',
        'is_show' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer'
    ];

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function translations()
    {
        return $this->hasMany(CategoryTranslation::class);
    }

    public function providers()
    {
        return $this->belongsToMany(Provider::class, 'provider_categories');
    }

    public function offers()
    {
        return $this->hasMany(Offer::class);
    }

    public function offerCategories()
    {
        return $this->hasMany(OfferCategory::class);
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

    // Recursive method to get all children categories
    public function allChildren()
    {
        return $this->children()->with('allChildren');
    }

    // Recursive method to get parent categories
    public function parents()
    {
        return $this->parent()->with('parents');
    }
}
