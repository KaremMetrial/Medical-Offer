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

    public function getWithActiveStories($user = null, $ip = null)
    {
        return $this->model->with([
            'translations',
            'stories' => function ($q) use ($user, $ip) {
                $q->active()
                    ->with(['views' => function ($vq) use ($user, $ip) {
                        $vq->when($user, fn($query) => $query->where('user_id', $user->id))
                           ->when(!$user && $ip, fn($query) => $query->where('ip_device', $ip))
                           ->when(!$user && !$ip, fn($query) => $query->whereRaw('1 = 0'));
                    }]);
            }
        ])
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

    public function getNextProviderIdWithStories(int $currentProviderId): ?int
    {
        return $this->model->where('id', '>', $currentProviderId)
            ->whereHas('stories', fn($q) => $q->active())
            ->orderBy('id', 'asc')
            ->value('id');
    }

    public function getFilteredPaginatedProviders(array $filters = [], int $perPage = 15)
    {
        return $this->model->with(['translations', 'country.translations', 'branches', 'offers.translations'])
            ->filterBySection($filters['section'] ?? null)
            ->filterByRating($filters['rating'] ?? null)
            ->filterByDiscount($filters['discount'] ?? null)
            ->paginate($perPage);
    }

    public function getProvidersByCategory($categoryId, array $filters = [], int $perPage = 15)
    {
        return $this->model->with(['translations', 'country.translations', 'branches', 'offers.translations'])
            ->whereHas('categories', fn($q) => $q->where('category_id', $categoryId))
            ->filterBySection($filters['section'] ?? null)
            ->filterByRating($filters['rating'] ?? null)
            ->filterByDiscount($filters['discount'] ?? null)
            ->paginate($perPage);
    }
}
