<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nationality extends Model
{
    use HasFactory;

    protected $fillable = [
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function translations()
    {
        return $this->hasMany(NationalityTranslation::class);
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

    public function getAllTranslations()
    {
        return $this->translations()->pluck('name', 'local');
    }
}
