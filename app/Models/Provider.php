<?php

namespace App\Models;

use App\Traits\Filterable;
use App\Traits\BelongsToCountry;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Provider extends Model
{
    use HasFactory, Filterable, BelongsToCountry;

    protected $fillable = [
        'section_id',
        'logo',
        'cover',
        'phone',
        'experince_years',
        'country_id',
        'status',
        'is_varified',
        'views'
    ];

    protected $casts = [
        'section_id' => 'integer',
        'country_id' => 'integer',
        'experince_years' => 'integer',
        'views' => 'integer',
        'is_varified' => 'boolean'
    ];

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function translations()
    {
        return $this->hasMany(ProviderTranslation::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'provider_categories');
    }

    public function branches()
    {
        return $this->hasMany(ProviderBranch::class);
    }

    public function offers()
    {
        return $this->hasMany(Offer::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_provider')
            ->withPivot('branch_id')
            ->withTimestamps();
    }

    public function branchUsers()
    {
        return $this->belongsToMany(User::class, 'user_provider')
            ->wherePivotNotNull('branch_id')
            ->withPivot('branch_id')
            ->withTimestamps();
    }

    public function mainUsers()
    {
        return $this->belongsToMany(User::class, 'user_provider')
            ->wherePivotNull('branch_id')
            ->withTimestamps();
    }

    // Get users who can manage specific branch
    public function usersForBranch($branchId)
    {
        return $this->users()->where('branch_id', $branchId);
    }

    // Check if provider has any managing users
    public function hasUsers()
    {
        return $this->users()->exists();
    }

    // Get all users with their branch assignments
    public function usersWithBranches()
    {
        return $this->users()->withPivot('branch_id');
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

    public function getTitleAttribute()
    {
        return $this->translation()?->title;
    }

    public function getDescriptionAttribute()
    {
        return $this->translation()?->description;
    }

    // Get main branch
    public function mainBranch()
    {
        // If branches are already loaded, use them to avoid N+1 queries
        if ($this->relationLoaded('branches')) {
            return $this->branches->firstWhere('is_main', true);
        }

        // Fall back to query if not loaded
        return $this->branches()->where('is_main', true)->first();
    }

    // Check if provider is active
    public function isActive()
    {
        return $this->status === 'active' && $this->is_varified;
    }
    public function stories()
    {
        return $this->hasMany(Story::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
    public function scopeSearchName($query, $search)
    {
        return $query->whereHas('translations', function ($q) use ($search) {
            $q->where('name', 'like', '%' . $search . '%');
        });
    }


    public function getImagePathAttribute(): ?string
    {
        return $this->logo;
    }

    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo ? asset('storage/' . $this->logo) : null;
    }

    public function getCoverUrlAttribute(): ?string
    {
        return $this->cover ? asset('storage/' . $this->cover) : null;
    }

    public function getSrcAttribute(): ?string
    {
        return $this->getLogoUrlAttribute();
    }
}
