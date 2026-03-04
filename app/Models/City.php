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
}
