<?php

namespace App\Repositories\Eloquent;

use App\Models\WalletTransaction;
use App\Repositories\Contracts\WalletTransactionRepositoryInterface;

class WalletTransactionRepository extends BaseRepository implements WalletTransactionRepositoryInterface
{
    public function __construct(WalletTransaction $model)
    {
        parent::__construct($model);
    }

    public function getRecentByUser($userId, int $limit = 5)
    {
        return $this->model->where('user_id', $userId)
            ->latest()
            ->take($limit)
            ->get();
    }

    public function getAllByUserPaginated($userId, int $perPage = 15)
    {
        return $this->model->where('user_id', $userId)
            ->latest()
            ->paginate($perPage);
    }

    public function createTransaction(array $data)
    {
        return $this->model->create($data);
    }
}
