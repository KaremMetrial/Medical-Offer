<?php

namespace App\Repositories\Contracts;

interface WithdrawalRepositoryInterface extends BaseRepositoryInterface
{
    public function getPendingByUser(int $userId);
}
