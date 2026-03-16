<?php

namespace App\Http\Controllers\Api;

use App\Repositories\Contracts\CountryRepositoryInterface;
use App\Http\Resources\CountryResource;
use Illuminate\Http\JsonResponse;

class CountryController extends BaseController
{
    protected $countryRepository;

    public function __construct(CountryRepositoryInterface $countryRepository)
    {
        $this->countryRepository = $countryRepository;
    }

    public function index(): JsonResponse
    {
        $countries = $this->countryRepository->all(['*'], ['translations']);
        return $this->successResponse([
            'label' => __('message.countries'),
            'countries' => CountryResource::collection($countries)
        ]);
    }
}
