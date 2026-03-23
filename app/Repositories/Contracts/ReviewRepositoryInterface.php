<?php

namespace App\Repositories\Contracts;

interface ReviewRepositoryInterface extends BaseRepositoryInterface
{
    public function getReviewsByProviderId($providerId);
    public function storeReview(array $data);
}

