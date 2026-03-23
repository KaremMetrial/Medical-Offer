<?php

namespace App\Repositories\Eloquent;

use App\Models\Withdrawal;
use App\Repositories\Contracts\WithdrawalRepositoryInterface;

class WithdrawalRepository extends BaseRepository implements WithdrawalRepositoryInterface
{
    public function __construct(Withdrawal $model)
    {
        parent::__construct($model);
    }

    public function getPendingByUser(int $userId)
    {
        return $this->model->where('user_id', $userId)
            ->where('status', \App\Enums\WithdrawalStatus::PENDING)
            ->get();
    }
}
