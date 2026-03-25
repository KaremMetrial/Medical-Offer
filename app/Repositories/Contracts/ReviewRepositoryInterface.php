<?php

namespace App\Repositories\Contracts;

interface ReviewRepositoryInterface extends BaseRepositoryInterface
{
    public function getReviewsByProviderId($providerId);
    public function storeReview(array $data);
    public function getAvgRatingByProviderId(int $providerId): float;
    public function getRecentForProvider(int $providerId, int $limit = 5, array $relations = []);
}

