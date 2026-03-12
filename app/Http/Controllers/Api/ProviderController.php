<?php

namespace App\Http\Controllers\Api;

use App\Repositories\Contracts\ProviderRepositoryInterface;
use App\Http\Resources\ProviderResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProviderController extends BaseController
{
    protected $providerRepository;

    public function __construct(ProviderRepositoryInterface $providerRepository)
    {
        $this->providerRepository = $providerRepository;
    }

    public function index(Request $request): JsonResponse
    {
        $providers = $this->providerRepository->paginate(15, ['*'], ['translations', 'country', 'branches']);
        return $this->successResponse([
            'label' => __('message.providers'),
            'items' => ProviderResource::collection($providers)
        ]);
    }

    public function show($id): JsonResponse
    {
        $provider = $this->providerRepository->findOrFail($id, ['*'], ['translations', 'country', 'branches', 'reviews']);
        return $this->successResponse(new ProviderResource($provider));
    }
}
