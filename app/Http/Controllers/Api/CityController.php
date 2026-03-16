<?php

namespace App\Http\Controllers\Api;

use App\Repositories\Contracts\CityRepositoryInterface;
use App\Http\Resources\CityResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CityController extends BaseController
{
    protected $cityRepository;

    public function __construct(CityRepositoryInterface $cityRepository)
    {
        $this->cityRepository = $cityRepository;
    }

    public function index(Request $request): JsonResponse
    {
        $cities = $this->cityRepository->all(['*'], ['translations']);
        return $this->successResponse([
            'label' => __('message.cities'),
            'cities' => CityResource::collection($cities)
        ]);
    }
}
