<?php

namespace App\Http\Controllers\Api;

use App\Repositories\Contracts\FavoriteRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FavoriteController extends BaseController
{
    protected $favoriteRepository;

    public function __construct(FavoriteRepositoryInterface $favoriteRepository)
    {
        $this->favoriteRepository = $favoriteRepository;
    }

    public function toggle(Request $request): JsonResponse
    {
        $request->validate([
            'offer_id' => 'required_without:provider_id|exists:offers,id',
            'provider_id' => 'required_without:offer_id|exists:providers,id',
        ]);

        $result = $this->favoriteRepository->toggleFavorite(auth('sanctum')->id(), $request->only(['offer_id', 'provider_id']));
        
        $message = $result['status'] === 'added' 
            ? __('message.added_to_favorites') 
            : __('message.removed_from_favorites');

        return $this->successResponse($result, $message);
    }

    public function index(): JsonResponse
    {
        $favorites = $this->favoriteRepository->getUserFavorites(auth('sanctum')->id());
        return $this->successResponse($favorites);
    }
}
