<?php

namespace App\Repositories\Eloquent;

use App\Models\City;
use App\Repositories\Contracts\CityRepositoryInterface;

class CityRepository extends BaseRepository implements CityRepositoryInterface
{
    public function __construct(City $model)
    {
        parent::__construct($model);
    }
    public function getFilteredCities($filters = [])
    {
        return $this->model->with(['translations'])
            ->filter($filters)
            ->where('is_active', true)
            ->get();
    }
}
