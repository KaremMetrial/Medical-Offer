<?php

namespace App\Repositories\Eloquent;

use App\Models\Review;
use App\Repositories\Contracts\ReviewRepositoryInterface;

class ReviewRepository extends BaseRepository implements ReviewRepositoryInterface
{
    public function __construct(Review $model)
    {
        parent::__construct($model);
    }

    public function getReviewsByProviderId($id, $columns = ['*'], $relations = [], $perPage = 10)
    {
        return $this->model->with($relations)
            ->where('provider_id', $id)
            ->with('offer.translations')
            ->latest()
            ->select($columns)
            ->paginate($perPage);
    }

    public function storeReview(array $data)
    {
        return $this->model->create(array_merge($data, [
            'status' => 'pending'
        ]));
    }

    /**
     * @inheritdoc
     */
    public function getAvgRatingByProviderId(int $providerId): float
    {
        return (float) ($this->model->where('provider_id', $providerId)->avg('rating') ?: 0);
    }

    /**
     * @inheritdoc
     */
    public function getRecentForProvider(int $providerId, int $limit = 5, array $relations = [])
    {
        return $this->model->with($relations)
            ->where('provider_id', $providerId)
            ->latest()
            ->take($limit)
            ->get();
    }
}

