<?php

namespace App\Repositories\Eloquent;

use App\Models\Provider;
use App\Repositories\Contracts\ProviderRepositoryInterface;

class ProviderRepository extends BaseRepository implements ProviderRepositoryInterface
{
    public function __construct(Provider $model)
    {
        parent::__construct($model);
    }

    public function getWithActiveStories()
    {
        return $this->model->with(['stories' => fn($q) => $q->active()])
            ->whereHas('stories', fn($q) => $q->active())
            ->get();
    }

    public function getEliteDoctors(int $limit = 10)
    {
        return $this->model->with(['translations', 'country'])
            ->with(['branches' => fn($q) => $q->where('is_main', true)])
            ->where('status', 'active')
            ->where('is_varified', true)
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->orderByDesc('reviews_count')
            ->take($limit)
            ->get();
    }

    public function getMedicalCenters(int $limit = 10)
    {
        return $this->model->with(['translations', 'country'])
            ->with(['branches' => fn($q) => $q->where('is_main', true)])
            ->where('status', 'active')
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->latest()
            ->take($limit)
            ->get();
    }
}
