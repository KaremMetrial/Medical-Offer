<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\ProviderStoryResource;
use App\Http\Resources\HomeStoryResource;
use App\Http\Resources\BannerResource;
use App\Http\Resources\CategoryResource;

use App\Http\Resources\ProviderResource;
use App\Http\Resources\AppBarResource;
use App\Http\Resources\HomeResource;
use App\Http\Resources\SectionResource;
use App\Repositories\Contracts\BannerRepositoryInterface;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Contracts\SectionRepositoryInterface;
use App\Repositories\Contracts\CountryRepositoryInterface;
use App\Repositories\Contracts\OfferRepositoryInterface;
use App\Repositories\Contracts\ProviderRepositoryInterface;
use App\Enums\SectionType;
use Illuminate\Support\Facades\Cache;

class HomeController extends BaseController
{
    public function __construct(
        protected  BannerRepositoryInterface $bannerRepository,
        protected  CategoryRepositoryInterface $categoryRepository,
        protected  SectionRepositoryInterface $sectionRepository,
        protected  OfferRepositoryInterface $offerRepository,
        protected  ProviderRepositoryInterface $providerRepository,
        protected  CountryRepositoryInterface $countryRepository
    ) {}

    /**
     * Handle the home page data request.
     */
    public function __invoke(Request $request)
    {
        $user = auth('sanctum')->user();
        $locale = app()->getLocale();

        // Cache non-user-specific data for 30 minutes
        $sharedData = Cache::remember("home_shared_data_{$locale}_v4", now()->addMinutes(30), function () {
            $sectionsAndFeatured = $this->getSectionsAndFeaturedData();
            
            return [
                'stories' => $this->getStoriesData(),
                'banners' => $this->getBannersData(),
                'sections' => $sectionsAndFeatured['sections'],
                'membership_banner' => $this->getMembershipBannerData(),
                'featured' => $sectionsAndFeatured['featured'],
            ];
        });

        return $this->successResponse(
            new HomeResource(array_merge([
                'appbar' => $this->getAppBarData($user),
            ], $sharedData)),
            __('home.retrieved_successfully')
        );
    }

    /**
     * Get app bar data.
     */
    private function getAppBarData($user = null): AppBarResource
    {
        if ($user) {
            return new AppBarResource($user);
        }

        $defaultCountry = $this->countryRepository->getDefaultCountry();

        return new AppBarResource([
            'title' => __('home.welcome_guest'),
            'subtitle' => __('home.guest_subtitle'),
            'avatar' => asset('storage/users/avatars/avatar.jpg'),
            'unread_notifications' => 0,
            'country_flag' => $defaultCountry?->flag_url,
            'search_placeholder' => __('home.search_placeholder'),
        ]);
    }

    /**
     * Get stories.
     */
    private function getStoriesData()
    {
        $providers = $this->providerRepository->getWithActiveStories();
        return ProviderStoryResource::collection($providers)->resolve();
    }

    /**
     * Get banners.
     */
    private function getBannersData()
    {
        $banners = $this->bannerRepository->getActiveBanners();
        return BannerResource::collection($banners)->resolve();
    }

    /**
     * Build dynamic sections and featured blocks.
     */
    private function getSectionsAndFeaturedData()
    {
        // Get sections with properly eager loaded relationships to avoid N+1 queries
        $sections = $this->sectionRepository->getHomeDataSections();
        
        $sectionsData = SectionResource::collection($sections)->resolve();
        $featured = [];
        
        foreach ($sections as $section) {
            if ($section->providers->isNotEmpty()) {
                $items = ProviderResource::collection($section->providers->take(10))->resolve();
                
                if (!empty($items)) {
                    $labelPrefix = match($section->type) {
                        SectionType::DOCTORS => __('home.sections.elite_doctors'),
                        SectionType::CENTERS => __('home.sections.medical_centers'),
                        default => $section->name
                    };

                    $featured[] = [
                        'label' => $labelPrefix,
                        'section_type' => $section->type->value,
                        'items' => $items
                    ];
                }
            }

            // Offers block — providers that have active published offers in this section
            if ($section->offers->isNotEmpty()) {
                // Get the unique providers that own these offers
                $providerIds = $section->offers->pluck('provider_id')->unique();
                $providersWithOffers = $section->providers->whereIn('id', $providerIds);

                $items = ProviderResource::collection($providersWithOffers->take(10))->resolve();

                if (!empty($items)) {
                    $featured[] = [
                        'label' => __('home.sections.care_offers'),
                        'section_type' => $section->type->value,
                        'items' => $items
                    ];
                }
            }
        }
        
        return [
            'sections' => $sectionsData,
            'featured' => $featured
        ];
    }

    /**
     * Get static membership banner data based on translation.
     */
    private function getMembershipBannerData()
    {
        return [
            'title' => __('home.membership_banner.title'),
            'subtitle' => __('home.membership_banner.subtitle'),
            'button_text' => __('home.membership_banner.button_text'),
            'background_gradient' => 'linear-gradient(90deg, #00BFFF 10%, #008AB8 65%, #005977 100%)',
            'button_bg_color' => '#FFC107',
            'action_type' => 'membership_details',
        ];
    }
}
