<?php

namespace App\Scopes;

use App\Services\CountryContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class OfferCountryScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $countryId = app(CountryContext::class)->getCountryId();
        
        if ($countryId) {
            $builder->whereHas('provider', function ($query) use ($countryId) {
                $query->where('country_id', $countryId);
            });
        }
    }
}
