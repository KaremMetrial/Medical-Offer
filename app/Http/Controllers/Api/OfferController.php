<?php

namespace App\Http\Controllers\Api;

use App\Repositories\Contracts\OfferRepositoryInterface;
use App\Http\Resources\OfferResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OfferController extends BaseController
{
    protected $offerRepository;

    public function __construct(OfferRepositoryInterface $offerRepository)
    {
        $this->offerRepository = $offerRepository;
    }

    public function index(Request $request): JsonResponse
    {
        $offers = $this->offerRepository->paginate(15, ['*'], ['translations', 'images', 'provider']);
        return $this->successResponse([
            'label' => __('message.offers'),
            'offers' => OfferResource::collection($offers)->response()->getData(true)
        ]);
    }

    public function show($id): JsonResponse
    {
        $offer = $this->offerRepository->findOrFail($id, ['*'], ['translations', 'images', 'provider', 'reviews']);
        return $this->successResponse(new OfferResource($offer));
    }
}
