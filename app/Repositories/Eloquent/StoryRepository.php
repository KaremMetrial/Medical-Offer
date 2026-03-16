<?php

namespace App\Repositories\Eloquent;

use App\Models\Story;
use App\Repositories\Contracts\StoryRepositoryInterface;

class StoryRepository extends BaseRepository implements StoryRepositoryInterface
{
    /**
     * StoryRepository constructor.
     *
     * @param Story $model
     */
    public function __construct(Story $model)
    {
        parent::__construct($model);
    }

    /**
     * Get stories by provider id
     *
     * @param int $providerId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getStoriesByProviderId($providerId)
    {
        return $this->model->where('provider_id', $providerId)->active()->latest()->take(10)->get();
    }

    public function getWithActiveStories($user = null, $ip = null)
    {
        return $this->model->active()
            ->with([
                'provider.translations',
                'views' => function ($vq) use ($user, $ip) {
                    $vq->when($user, fn($query) => $query->where('user_id', $user->id))
                       ->when(!$user && $ip, fn($query) => $query->where('ip_device', $ip))
                       ->when(!$user && !$ip, fn($query) => $query->whereRaw('1 = 0'));
                }
            ])
            ->latest()
            ->get();
    }

    public function recordView($storyId, array $data)
    {
        $story = $this->findOrFail($storyId);
        
        $search = [
            'story_id' => $story->id,
        ];

        if (isset($data['user_id'])) {
            $search['user_id'] = $data['user_id'];
        } else {
            $search['ip_device'] = $data['ip_device'] ?? null;
            $search['user_id'] = null; // Ensure we match guest views separately
        }

        return $story->views()->updateOrCreate($search, [
            'duration' => $data['duration'] ?? 0,
            'ip_device' => $data['ip_device'] ?? null,
        ]);
    }
}
