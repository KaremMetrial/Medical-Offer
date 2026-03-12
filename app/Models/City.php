<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    protected $fillable = [
        'governorate_id',
        'is_active',
        'local'
    ];

    protected $casts = [
        'governorate_id' => 'integer',
        'is_active' => 'boolean'
    ];

    public function governorate()
    {
        return $this->belongsTo(Governorate::class);
    }

    public function translations()
    {
        return $this->hasMany(CityTranslation::class);
    }

    public function branches()
    {
        return $this->hasMany(ProviderBranch::class);
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
    public function scopeSearchName($query, $search)
    {
        return $query->whereHas('translations', function ($q) use ($search) {
            $q->where('name', 'like', '%' . $search . '%');
        });
    }
}
