<?php

namespace App\Http\Controllers\Api;

use App\Repositories\Contracts\GovernorateRepositoryInterface;
use App\Http\Resources\GovernorateResource;
use Illuminate\Http\JsonResponse;

class GovernorateController extends BaseController
{
    protected $governorateRepository;

    public function __construct(GovernorateRepositoryInterface $governorateRepository)
    {
        $this->governorateRepository = $governorateRepository;
    }

    public function index(\App\Filters\GovernorateFilter $filter): JsonResponse
    {
        $governorates = $this->governorateRepository->getFilteredGovernorates($filter);
        return $this->successResponse([
            'label' => __('message.governorates'),
            'governorates' => GovernorateResource::collection($governorates)
        ]);
    }
}
