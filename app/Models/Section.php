<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory;

    protected $fillable = [
        'icon',
        'type',
        'sort_order',
        'is_active'
    ];

    protected $casts = [
        'type' => \App\Enums\SectionType::class,
        'sort_order' => 'integer',
        'is_active' => 'boolean'
    ];

    public function translations()
    {
        return $this->hasMany(SectionTranslation::class);
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function providers()
    {
        return $this->hasMany(Provider::class);
    }

    public function offers()
    {
        return $this->hasManyThrough(Offer::class, Provider::class);
    }

    public function translation($locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        
        if ($this->relationLoaded('translations')) {
            return $this->translations->firstWhere('local', $locale) ?? $this->translations->first();
        }
        
        return $this->translations()->where('local', $locale)->first()
            ?? $this->translations()->first();
    }

    public function getNameAttribute()
    {
        return $this->translation()?->name;
    }

    public function scopeSearchName($query, $search)
    {
        return $query->whereHas('translations', function ($q) use ($search) {
            $q->where('name', 'like', '%' . $search . '%');
        });
    }

    public function getSrcAttribute(): ?string
    {
        return $this->icon ? asset('storage/' . $this->icon) : null;
    }
}
