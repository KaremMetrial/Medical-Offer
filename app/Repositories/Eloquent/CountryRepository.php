<?php

namespace App\Repositories\Eloquent;

use App\Models\Country;
use App\Repositories\Contracts\CountryRepositoryInterface;

class CountryRepository extends BaseRepository implements CountryRepositoryInterface
{
    public function __construct(Country $model)
    {
        parent::__construct($model);
    }

    public function getDefaultCountry()
    {
        return \Illuminate\Support\Facades\Cache::remember('default_country', now()->addDay(), function () {
            $defaultCountryId = config('settings.default_country_id', 1);
            return $this->model->find($defaultCountryId) ?? $this->model->first();
        });
    }
}
