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
}
