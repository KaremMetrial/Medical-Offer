<?php

namespace App\Repositories\Contracts;

interface GovernorateRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get default governorate from settings.
     *
     * @return \App\Models\Governorate|null
     */
    public function getDefaultGovernorate();

    public function getFilteredGovernorates($filters = []);
}
