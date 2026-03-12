<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory;

    protected $fillable = [
        'image_path',
        'link_type',
        'link_id',
        'external_url',
        'start_date',
        'end_date',
        'position',
        'is_active',
        'sort_order'
    ];

    protected $casts = [
        'link_id' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
        'sort_order' => 'integer'
    ];

    // Translations relationship
    public function translations()
    {
        return $this->hasMany(BannerTranslation::class);
    }

    // Get translation for current locale
    public function translation($locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        return $this->translations()->where('local', $locale)->first()
            ?? $this->translations()->first();
    }

    // Get title in current locale
    public function getTitleAttribute()
    {
        return $this->translation()?->title;
    }

    // Get all translations
    public function getAllTranslations()
    {
        return $this->translations()->pluck('title', 'local');
    }

    // Get banner image URL
    public function getImageUrlAttribute()
    {
        return $this->image_path ? asset('storage/' . $this->image_path) : null;
    }

    public function getSrcAttribute()
    {
        return $this->getImageUrlAttribute();
    }

    // Get link URL
    public function getLinkUrlAttribute()
    {
        switch ($this->link_type) {
            case 'offer':
                return $this->link_id ? route('offers.show', $this->link_id) : $this->external_url;
            case 'provider':
                return $this->link_id ? route('providers.show', $this->link_id) : $this->external_url;
            case 'category':
                return $this->link_id ? route('categories.show', $this->link_id) : $this->external_url;
            case 'external':
            default:
                return $this->external_url;
        }
    }

    // Check if banner is active
    public function isActive()
    {
        return $this->is_active &&
            $this->start_date <= now() &&
            $this->end_date >= now();
    }

    public function scopeSearchTitle($query, $search)
    {
        return $query->whereHas('translations', function ($q) use ($search) {
            $q->where('title', 'like', '%' . $search . '%');
        });
    }
}
