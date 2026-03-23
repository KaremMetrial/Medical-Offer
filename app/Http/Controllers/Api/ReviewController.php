<?php

namespace App\Http\Controllers\Api;

use App\Repositories\Contracts\ReviewRepositoryInterface;
use App\Http\Resources\{ReviewResource,PaginationResource};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
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

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'provider_id' => 'required_without:offer_id|exists:providers,id',
            'offer_id' => 'required_without:provider_id|exists:offers,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first());
        }

        $this->reviewRepository->storeReview(array_merge($request->only([
            'provider_id', 'offer_id', 'rating', 'comment'
        ]), ['user_id' => $request->user()->id]));

        return $this->successResponse(null, __('message.review_submitted_successfully'));
    }
}

