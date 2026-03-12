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
            // 'categories' => fn($q) => $q->where('is_active', true)
            //     ->where('is_show', true)
            //     ->with('translations')
            //     ->orderBy('sort_order')
            //     ->take(12),
            'providers' => fn($q) => $q->where('providers.status', 'active')
                ->with(['translations', 'country'])
                ->with(['branches' => fn($bq) => $bq->where('is_main', true)])
                ->withCount('reviews')
                ->withAvg('reviews', 'rating')
                ->orderByDesc('is_varified')
                ->orderByDesc('reviews_avg_rating'),
            'offers' => fn($q) => $q->where('offers.status', 'published')
                ->with(['translations', 'provider.translations', 'provider.branches' => fn($bq) => $bq->where('is_main', true)])
                ->where('offers.show_in_home', true)
                ->orderBy('offers.sort_order'),
        ])
        ->where('is_active', true)
        ->orderBy('sort_order')
        ->get();
    }
}
