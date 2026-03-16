<?php

namespace App\Repositories\Contracts;

interface MemberPlanRepositoryInterface extends BaseRepositoryInterface
{
    public function getActivePlans($countryId = null);
}
