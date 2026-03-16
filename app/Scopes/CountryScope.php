<?php

namespace App\Scopes;

use App\Services\CountryContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class CountryScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $countryContext = app(CountryContext::class);
        
        if ($countryContext->hasCountryId()) {
            // Apply filtering by country_id
            // Using the table name to avoid ambiguity in joins
            $builder->where($model->getTable() . '.country_id', $countryContext->getCountryId());
        }
    }
}
