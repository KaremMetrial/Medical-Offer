<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Governorate extends Model
{
    use HasFactory;

    protected $fillable = [
        'country_id',
        'is_active',
        'local'
    ];

    protected $casts = [
        'country_id' => 'integer',
        'is_active' => 'boolean'
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function translations()
    {
        return $this->hasMany(GovernorateTranslation::class);
    }

    public function cities()
    {
        return $this->hasMany(City::class);
    }

    public function branches()
    {
        return $this->hasMany(ProviderBranch::class);
    }

    // Helper method to get translation
    public function translation($locale = null)
    {
        $locale = $locale ?? app()->getLocale();
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
