<?php

namespace App\Http\Controllers\Api;

use App\Repositories\Contracts\ReviewRepositoryInterface;
use App\Http\Resources\{ReviewResource,PaginationResource};
use Illuminate\Http\JsonResponse;

class ReviewController extends BaseController
{
    protected $reviewRepository;

    public function __construct(ReviewRepositoryInterface $reviewRepository)
    {
        $this->reviewRepository = $reviewRepository;
    }

    public function getReviewsByProviderId($providerId): JsonResponse
    {
        $reviews = $this->reviewRepository->getReviewsByProviderId($providerId);
        return $this->successResponse([
            'reviews' => ReviewResource::collection($reviews),
            'pagination' => new PaginationResource($reviews)
        ]);
    }
}
