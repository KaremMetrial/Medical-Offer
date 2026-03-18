<?php

namespace App\Repositories\Eloquent;

use App\Models\CardRequest;
use App\Repositories\Contracts\CardRequestRepositoryInterface;

class CardRequestRepository extends BaseRepository implements CardRequestRepositoryInterface
{
    public function __construct(CardRequest $model)
    {
        parent::__construct($model);
    }
    
    public function getLatestByUser($userId)
    {
        return $this->model->where('user_id', $userId)
            ->with(['governorate.translations', 'city.translations'])
            ->latest()
            ->first();
    }

    public function hasUserRequestedBefore($userId): bool
    {
        return $this->model->where('user_id', $userId)
            ->where('status', '!=', 'cancelled')
            ->exists();
    }
}
