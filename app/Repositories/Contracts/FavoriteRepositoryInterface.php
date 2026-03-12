<?php

namespace App\Repositories\Contracts;

interface FavoriteRepositoryInterface extends BaseRepositoryInterface
{
    public function toggleFavorite(int $userId, array $data);
    public function getUserFavorites(int $userId);
}
