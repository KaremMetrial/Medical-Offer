<?php

namespace App\Repositories\Contracts;

interface CityRepositoryInterface extends BaseRepositoryInterface
{
    public function getFilteredCities($filters = []);
}
