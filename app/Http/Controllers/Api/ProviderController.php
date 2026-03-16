<?php

namespace App\Http\Controllers\Api;

use App\Repositories\Contracts\{
    ProviderRepositoryInterface,
    CategoryRepositoryInterface,
    GovernorateRepositoryInterface
};
use App\Http\Resources\{
    ProviderResource,
    ProviderDetailsResource,
    PaginationResource,
    GovernorateResource,
    OfferResource
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
use App\Filters\ProviderFilter;

class ProviderController extends BaseController
{

    public function __construct(
        private ProviderRepositoryInterface $providerRepository,
        private CategoryRepositoryInterface $categoryRepository,
        private GovernorateRepositoryInterface $governorateRepository
    ) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->validate([
            'section' => 'nullable|exists:sections,type',
            'rating' => 'nullable|in:' . implode(',', RatingType::values()),
            'discount' => 'nullable|in:' . implode(',', DiscountType::values()),
            'governorate_id' => 'nullable|exists:governorates,id'
        ]);
        $providers = $this->providerRepository->getFilteredPaginatedProviders(new ProviderFilter($request), 15);
        $governorates = $this->governorateRepository->all(['*'], ['translations']);
        
        return $this->successResponse([
            'title' => __('message.providers'),
            'sub_title' => __('message.providers'),
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

    public function show($id): JsonResponse
    {
        $provider = $this->providerRepository->getDetails($id);        

        // Increment views (visits)
        $provider->increment('views');

        $titleKey = $this->getProviderLabel($provider->section?->type);

        return $this->successResponse([
            'provider' => new ProviderDetailsResource($provider),
            'offers' => OfferResource::collection($provider->offers),
            'labels' => [
                'title' => __($titleKey),
                'sub_title' => $provider->name,
                'visits_count' => __('message.visits_count'),
                'rating' => __('message.rating'),
                'experience_years' => __('message.experience_years'),
                'discounts_up_to' => __('message.discounts_up_to'),
                'contact_details' => __('message.contact_details'),
                'visitor_reviews' => __('message.visitor_reviews'),
                'discounts' => __('message.discounts'),
                'call_now' => __('message.call_now'),
                'clinic_location' => __('message.clinic_location'),
            ],
        ]);
    }
    private function getProviderLabel($sectionType)
    {
        return match($sectionType) {
            SectionType::DOCTORS => 'message.details_doctors',
            SectionType::LABS => 'message.details_labs',
            SectionType::PHARMACIES => 'message.details_pharmacies',
            SectionType::CENTERS => 'message.details_centers',
            default => 'message.provider_details',
        };
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
        $providers = $this->providerRepository->getProvidersByCategory($categoryId, new ProviderFilter($request), 15);
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
