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

    public function index(): JsonResponse
    {
        $governorates = $this->governorateRepository->all(['*'], ['translations']);
        return $this->successResponse([
            'label' => __('message.governorates'),
            'governorates' => GovernorateResource::collection($governorates)
        ]);
    }
}
