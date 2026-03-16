<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\NationalityResource;
use Illuminate\Http\JsonResponse;
use App\Repositories\Contracts\NationalityRepositoryInterface;

class NationalityController extends BaseController
{
    protected $nationalityRepository;

    public function __construct(NationalityRepositoryInterface $nationalityRepository)
    {
        $this->nationalityRepository = $nationalityRepository;
    }

    public function index(): JsonResponse
    {
        $nationalities = $this->nationalityRepository->all(['*'], ['translations']);
        return $this->successResponse([
            'label' => __('message.nationalities'),
            'nationalities' => NationalityResource::collection($nationalities)
        ]);
    }
}
