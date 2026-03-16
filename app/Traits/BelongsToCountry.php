<?php

namespace App\Traits;

use App\Scopes\CountryScope;

trait BelongsToCountry
{
    /**
     * Boot the BelongsToCountry trait for a model.
     */
    public static function bootBelongsToCountry(): void
    {
        static::addGlobalScope(new CountryScope());
    }

    /**
     * Get the country associated with the model.
     */
    public function country()
    {
        return $this->belongsTo(\App\Models\Country::class);
    }
}
