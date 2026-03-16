<?php

namespace App\Repositories\Eloquent;

use App\Models\MemberPlan;
use App\Repositories\Contracts\MemberPlanRepositoryInterface;

class MemberPlanRepository extends BaseRepository implements MemberPlanRepositoryInterface
{
    public function __construct(MemberPlan $model)
    {
        parent::__construct($model);
    }

    public function getActivePlans($countryId = null)
    {
        $query = $this->model
            ->where('is_active', true)
            ->with(['translations']);

        if ($countryId) {
            $query->where('country_id', $countryId);
        }

        return $query->get();
    }
}
