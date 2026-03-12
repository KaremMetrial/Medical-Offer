<?php

namespace App\Repositories\Contracts;

interface CountryRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get default country from settings.
     *
     * @return \App\Models\Country|null
     */
    public function getDefaultCountry();
}
