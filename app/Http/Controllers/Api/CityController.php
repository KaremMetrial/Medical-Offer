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

    public function index(\App\Filters\CityFilter $filter): JsonResponse
    {
        $cities = $this->cityRepository->getFilteredCities($filter);
        return $this->successResponse([
            'label' => __('message.cities'),
            'cities' => CityResource::collection($cities)
        ]);
    }
}
