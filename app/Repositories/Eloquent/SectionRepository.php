<?php

namespace App\Repositories\Eloquent;

use App\Models\Section;
use App\Repositories\Contracts\SectionRepositoryInterface;

class SectionRepository extends BaseRepository implements SectionRepositoryInterface
{
    public function __construct(Section $model)
    {
        parent::__construct($model);
    }

    public function getActiveSections()
    {
        return $this->model->with('translations')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }

    public function getHomeDataSections()
    {
        return $this->model->with([
            'translations',
            'providers' => fn($q) => $q->where('providers.status', 'active')
                ->with(['translations', 'country.translations'])
                ->with(['branches' => fn($bq) => $bq->where('is_main', true)])
                ->withCount('reviews')
                ->withAvg('reviews', 'rating')
                ->withMax('offers', 'discount_percent')
                ->orderByDesc('is_varified')
                ->orderByDesc('reviews_avg_rating'),
            'offers' => fn($q) => $q->where('offers.status', 'published')
                ->where('offers.show_in_home', true)
                ->with(['translations'])
                ->orderBy('offers.sort_order'),
        ])
        ->where('is_active', true)
        ->orderBy('sort_order')
        ->get();
    }
}
