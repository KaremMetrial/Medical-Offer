<?php

namespace App\Repositories\Contracts;

interface CardRequestRepositoryInterface extends BaseRepositoryInterface
{
    public function getLatestByUser($userId);
    public function hasUserRequestedBefore($userId): bool;
}
