<?php

namespace App\Repositories\Eloquent;

use App\Models\Governorate;
use App\Repositories\Contracts\GovernorateRepositoryInterface;

class GovernorateRepository extends BaseRepository implements GovernorateRepositoryInterface
{
    public function __construct(Governorate $model)
    {
        parent::__construct($model);
    }

    public function getDefaultGovernorate()
    {
        return \Illuminate\Support\Facades\Cache::remember('default_governorate', now()->addDay(), function () {
            $defaultGovernorateId = config('settings.default_governorate_id', 1);
            return $this->model->find($defaultGovernorateId) ?? $this->model->first();
        });
    }
    public function getFilteredGovernorates($filters = [])
    {
        return $this->model->with(['translations'])
            ->filter($filters)
            ->where('is_active', true)
            ->get();
    }
}
