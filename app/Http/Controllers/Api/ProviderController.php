<?php

namespace App\Http\Controllers\Api;

use App\Repositories\Contracts\{
    ProviderRepositoryInterface,
    CategoryRepositoryInterface,
    GovernorateRepositoryInterface,
    ReviewRepositoryInterface
};
use App\Http\Resources\{
    ProviderResource,
    ProviderDetailsResource,
    PaginationResource,
    GovernorateResource,
    OfferResource,
    ReviewResource,
    Filters\GovernorateFilterResource
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
        private GovernorateRepositoryInterface $governorateRepository,
        private ReviewRepositoryInterface $reviewRepository 
    ) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $this->filterValidation($request);
        $providers = $this->providerRepository->getFilteredPaginatedProviders(new ProviderFilter($request), 15);
        $governorates = $this->governorateRepository->all(['*'], ['translations']);

        return $this->successResponse([
            'title' => __('message.providers'),
            'sub_title' => __('message.providers'),
            'providers' => ProviderResource::collection($providers),
            'pagination' => new PaginationResource($providers),
            'filters' => $this->filterOptions($request, $governorates),
        ]);
    }
    private function filterOptions($request, $governorates = null)
    {
        $governorates = $governorates ?? $this->governorateRepository->all(['*'], ['translations']);
        return [
            SectionType::optionsWithSelected($request->section),
            RatingType::optionsWithSelected($request->rating),
            DiscountType::optionsWithSelected($request->discount),
            [
                'items' => GovernorateFilterResource::collection($governorates),
                'selected' => $request->governorate_id,
                'selected_label' => $request->governorate_id ? $governorates->find($request->governorate_id)->name : null,
                'label' => __('message.governorate'),
                'key'=> 'governorate'
            ],
        ];
    }
    public function show($id): JsonResponse
    {
        $provider = $this->providerRepository->getDetails($id);
        $reviews = $this->reviewRepository->getReviewsByProviderId($id);
        // Increment views (visits)
        $provider->increment('views');

        $titleKey = $this->getProviderLabel($provider->section?->type);

        return $this->successResponse([
            'provider' => new ProviderDetailsResource($provider),
            'offers' => OfferResource::collection($provider->offers),
            'reviews' => ReviewResource::collection($reviews),
            'pagination' => new PaginationResource($reviews),
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
        return match ($sectionType) {
            SectionType::DOCTORS => 'message.details_doctors',
            SectionType::LABS => 'message.details_labs',
            SectionType::PHARMACIES => 'message.details_pharmacies',
            SectionType::CENTERS => 'message.details_centers',
            default => 'message.provider_details',
        };
    }
    public function getProvidersByCategory($categoryId, Request $request): JsonResponse
    {
        $filters = $this->filterValidation($request);
        $category = $this->categoryRepository->findOrFail($categoryId, ['*'], ['parent.translations']);
        $governorates = $this->governorateRepository->all(['*'], ['translations']);
        $providers = $this->providerRepository->getProvidersByCategory($categoryId, new ProviderFilter($request), 15);
        return $this->successResponse([
            'title' => $category->parent?->name,
            'type' => $category->parent?->section?->type ?? $category->section?->type,
            'sub_title' => $category->name,
            'providers' => ProviderResource::collection($providers),
            'pagination' => new PaginationResource($providers),
            'filters' => $this->filterOptions($request, $governorates),
        ]);
    }
    private function filterValidation($request)
    {
        return $request->validate([
            'section' => 'nullable|in:' . implode(',', SectionType::values()),
            'rating' => 'nullable|in:' . implode(',', RatingType::values()),
            'discount' => 'nullable|in:' . implode(',', DiscountType::values()),
            'governorate_id' => 'nullable|exists:governorates,id'
        ]);
    }
}
