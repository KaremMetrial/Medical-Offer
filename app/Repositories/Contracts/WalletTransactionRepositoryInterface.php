<?php

namespace App\Repositories\Contracts;

interface WalletTransactionRepositoryInterface extends BaseRepositoryInterface
{
    public function getRecentByUser($userId, int $limit = 5);

    public function getAllByUserPaginated($userId, int $perPage = 15);

    public function createTransaction(array $data);
}
