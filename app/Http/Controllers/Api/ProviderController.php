<?php

namespace App\Http\Controllers\Api;

use App\Repositories\Contracts\{
    ProviderRepositoryInterface,
    CategoryRepositoryInterface,
    GovernorateRepositoryInterface
};
use App\Http\Resources\{
    ProviderResource,
    PaginationResource,
    GovernorateResource
};
use Illuminate\Http\{
    JsonResponse,
    Request
};
use App\Enums\{
    SectionType,
    RatingType,
    DiscountType
};

class ProviderController extends BaseController
{

    public function __construct(
        private ProviderRepositoryInterface $providerRepository,
        private CategoryRepositoryInterface $categoryRepository,
        private GovernorateRepositoryInterface $governorateRepository
    ) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['section', 'rating', 'discount']);
        $providers = $this->providerRepository->getFilteredPaginatedProviders($filters, 15);
        
        return $this->successResponse([
            'title' => __('message.providers'),
            'sub_title' => __('message.providers'),
            'providers' => ProviderResource::collection($providers),
            'pagination' => new PaginationResource($providers),
            'filters' => [
                'section' => SectionType::options(),
                'rating' => RatingType::options(),
                'discount' => DiscountType::options(),
            ],
            'selected_filters' => [
                'section' => [
                    'name' => $request->section ? SectionType::getLabelByValue($request->section) : null,
                    'value' => $request->section,
                ],
                'rating' => [
                    'name' => $request->rating ? RatingType::getLabelByValue($request->rating) : null,
                    'value' => $request->rating,
                ],
                'discount' => [
                    'name' => $request->discount ? DiscountType::getLabelByValue($request->discount) : null,
                    'value' => $request->discount,
                ],
                'governorate_id' => [
                    'name' => $request->governorate_id ? $this->governorateRepository->findOrFail($request->governorate_id, ['*'], ['translations'])->name : null,
                    'value' => $request->governorate_id,
                ],
            ]
        ]);
    }

    public function show($id): JsonResponse
    {
        $provider = $this->providerRepository->findOrFail($id, ['*'], ['translations', 'country', 'branches', 'reviews']);
        return $this->successResponse(new ProviderResource($provider));
    }

    public function getProvidersByCategory($categoryId, Request $request): JsonResponse
    {
        $filters = $request->validate([
            'section' => 'nullable|exists:sections,type',
            'rating' => 'nullable|in:' . implode(',', RatingType::values()),
            'discount' => 'nullable|in:' . implode(',', DiscountType::values()),
            'governorate_id' => 'nullable|exists:governorates,id'
        ]);
        $category = $this->categoryRepository->findOrFail($categoryId, ['*'], ['parent.translations']);
        $governorates = $this->governorateRepository->all(['*'], ['translations']);
        $providers = $this->providerRepository->getProvidersByCategory($categoryId, $filters, 15);
        return $this->successResponse([
            'title' => $category->parent?->name,
            'sub_title' => $category->name,
            'providers' => ProviderResource::collection($providers),
            'pagination' => new PaginationResource($providers),
            'filters' => [
                'section' => SectionType::options(),
                'rating' => RatingType::options(),
                'discount' => DiscountType::options(),
                'governorate_id' => GovernorateResource::collection($governorates),
            ],
            'selected_filters' => [
                'section' => [
                    'name' => $request->section ? SectionType::getLabelByValue($request->section) : null,
                    'value' => $request->section,
                ],
                'rating' => [
                    'name' => $request->rating ? RatingType::getLabelByValue($request->rating) : null,
                    'value' => $request->rating,
                ],  
                'discount' => [
                    'name' => $request->discount ? DiscountType::getLabelByValue($request->discount) : null,
                    'value' => $request->discount,
                ],
                'governorate_id' => [
                    'name' => $request->governorate_id ? $this->governorateRepository->findOrFail($request->governorate_id, ['*'], ['translations'])->name : null,
                    'value' => $request->governorate_id,
                ],
            ]
        ]);
    }
}
