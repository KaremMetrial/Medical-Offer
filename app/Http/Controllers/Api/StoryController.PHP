<?php

namespace App\Http\Controllers\Api;

use App\Repositories\Contracts\StoryRepositoryInterface;
use App\Http\Resources\StoryResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;
use App\Repositories\Contracts\ProviderRepositoryInterface;
use App\Http\Resources\ProviderStoryResource;

class StoryController extends BaseController
{
    public function __construct(
        private StoryRepositoryInterface $storyRepository,
        private ProviderRepositoryInterface $providerRepository
    ){}
    
    public function getStoriesByProviderId(Request $request, $providerId): JsonResponse
    {
        $user = auth('sanctum')->user();
        $ip = $request->header('Ip-Device');

        $provider = $this->providerRepository->findOrFail($providerId, ['*'], [
            'translations',
            'stories' => function ($q) use ($user, $ip) {
                $q->active()
                    ->latest()
                    ->take(10)
                    ->with(['views' => function ($vq) use ($user, $ip) {
                        $vq->when($user, fn($query) => $query->where('user_id', $user->id))
                           ->when(!$user && $ip, fn($query) => $query->where('ip_device', $ip))
                           ->when(!$user && !$ip, fn($query) => $query->whereRaw('1 = 0'));
                    }]);
            }
        ]);

        return $this->successResponse(
            new ProviderStoryResource($provider),
            __('stories.retrieved_successfully')
        );
    }

    public function index(Request $request): JsonResponse
    {
        $user = auth('sanctum')->user();
        $ip = $request->header('Ip-Device');

        $stories = $this->storyRepository->getWithActiveStories($user, $ip);

        return $this->successResponse(
            StoryResource::collection($stories),
            __('stories.retrieved_successfully')
        );
    }

    public function show(Request $request, $id): JsonResponse
    {
        $user = auth('sanctum')->user();
        $ip = $request->header('Ip-Device');

        $story = $this->storyRepository->findOrFail($id, ['*'], [
            'translations',
            'views' => function ($vq) use ($user, $ip) {
                $vq->when($user, fn($query) => $query->where('user_id', $user->id))
                   ->when(!$user && $ip, fn($query) => $query->where('ip_device', $ip))
                   ->when(!$user && !$ip, fn($query) => $query->whereRaw('1 = 0'));
            }
        ]);

        return $this->successResponse(
            new StoryResource($story),
            __('stories.retrieved_successfully')
        );
    }

    public function recordView(Request $request, $id): JsonResponse
    {
        $user = auth('sanctum')->user();
        
        $viewData = [
            'user_id' => $user?->id,
            'ip_device' => $request->header('Ip-Device'),
            'duration' => $request->input('duration', 0),
        ];

        $this->storyRepository->recordView($id, $viewData);

        return $this->successResponse(
            null,
            __('stories.view_recorded_successfully')
        );
    }
}
