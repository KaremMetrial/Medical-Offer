<?php

namespace App\Repositories\Eloquent;

use App\Models\Offer;
use App\Repositories\Contracts\OfferRepositoryInterface;

class OfferRepository extends BaseRepository implements OfferRepositoryInterface
{
    public function __construct(Offer $model)
    {
        parent::__construct($model);
    }

    public function getCareOffers(int $limit = 10)
    {
        return $this->model->with(['translations', 'images', 'provider' => function ($q) {
                $q->with(['translations', 'country'])->with(['branches' => fn($qb) => $qb->where('is_main', true)]);
            }])
            ->where('status', 'published')
            ->where('show_in_home', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->latest()
            ->take($limit)
            ->get();
    }

    public function getOffersByProviderId($id, $columns = ['*'], $relations = [])
    {
        return $this->model->with($relations)
            ->where('provider_id', $id)
            ->where('status', 'published')
            // ->where('show_in_home', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            // ->withCount('reviews')
            // ->withAvg('reviews', 'rating')
            ->latest()
            ->select($columns)
            ->get();
    }
}
