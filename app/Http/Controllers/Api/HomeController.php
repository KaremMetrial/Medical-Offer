<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\ProviderStoryResource;
use App\Http\Resources\HomeStoryResource;
use App\Http\Resources\BannerResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\OfferResource;
use App\Http\Resources\ProviderResource;
use App\Models\Country;
use App\Models\Provider;
use App\Models\Story;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Offer;

use Illuminate\Support\Facades\Cache;

class HomeController extends BaseController
{
    /**
     * Handle the home page data request.
     */
    public function __invoke(Request $request)
    {
        $user = auth('sanctum')->user();
        $locale = app()->getLocale();

        // Cache non-user-specific data for 30 minutes to improve performance
        $sharedData = Cache::remember("home_shared_data_{$locale}", now()->addMinutes(30), function () {
            return [
                'stories' => $this->getStoriesData(),
                'banners' => $this->getBannersData(),
                'categories' => $this->getCategoriesData(),
                'elite_doctors' => $this->getEliteDoctorsData(),
                'medical_centers' => $this->getMedicalCentersData(),
                'care_offers' => $this->getCareOffersData(),
            ];
        });

        return $this->successResponse(
            array_merge([
                'appbar' => $this->getAppBarData($user),
            ], $sharedData),
            __('home.retrieved_successfully')
        );
    }

    /**
     * Get data for the top app bar.
     */
    private function getAppBarData($user = null): array
    {
        if ($user) {
            return [
                'title' => __('home.welcome_back', ['name' => $user->name]),
                'subtitle' => __('home.good_day'),
                'avatar' => $user->avatar_url,
                'unread_notifications' => $user->unreadNotificationsCount(),
                'country_flag' => $user->country?->flag_url,
                'search_placeholder' => __('home.search_placeholder'),
            ];
        }

        $defaultCountryId = config('settings.default_country_id', 1);
        $defaultCountry = Country::find($defaultCountryId) ?? Country::first();

        return [
            'title' => __('home.welcome_guest'),
            'subtitle' => __('home.guest_subtitle'),
            'avatar' => asset('storage/users/avatars/avatar.jpg'),
            'unread_notifications' => 0,
            'country_flag' => $defaultCountry?->flag_url,
            'search_placeholder' => __('home.search_placeholder'),
        ];
    }

    /**
     * Get active stories from providers.
     */
    private function getStoriesData()
    {
        $providers = Provider::with(['stories' => fn($q) => $q->active()])
            ->whereHas('stories', fn($q) => $q->active())
            ->get();

        return ProviderStoryResource::collection($providers)->resolve();
    }

    /**
     * Get active banners for slider.
     */
    private function getBannersData()
    {
        $banners = Banner::with('translations')
            ->where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->orderBy('sort_order')
            ->get();

        return BannerResource::collection($banners)->resolve();
    }

    /**
     * Get main categories for home page.
     */
    private function getCategoriesData()
    {
        $categories = Category::with('translations')
            ->where('is_active', true)
            ->where('is_show', true)
            ->orderBy('sort_order')
            ->get();

        return [
            'label' => __('home.sections.categories'),
            'items' => CategoryResource::collection($categories)->resolve(),
        ];
    }

    /**
     * Get elite (verified/highly rated) doctors.
     */
    private function getEliteDoctorsData()
    {
        $doctors = Provider::with(['translations', 'reviews', 'country'])
            ->with(['branches' => fn($q) => $q->where('is_main', true)])
            ->where('status', 'active')
            ->where('is_varified', true)
            ->withCount('reviews')
            ->orderByDesc('reviews_count')
            ->take(10)
            ->get();

        return [
            'label' => __('home.sections.elite_doctors'),
            'items' => ProviderResource::collection($doctors)->resolve(),
        ];
    }

    /**
     * Get medical centers.
     */
    private function getMedicalCentersData()
    {
        $centers = Provider::with(['translations', 'reviews', 'country'])
            ->with(['branches' => fn($q) => $q->where('is_main', true)])
            ->where('status', 'active')
            // Add any logic to filter by 'center' type if available in the future
            ->latest()
            ->take(10)
            ->get();

        return [
            'label' => __('home.sections.medical_centers'),
            'items' => ProviderResource::collection($centers)->resolve(),
        ];
    }

    /**
     * Get Care & Beauty offers for home page.
     */
    private function getCareOffersData()
    {
        $offers = Offer::with(['translations', 'images', 'provider' => function ($q) {
            $q->with(['translations', 'country'])->with(['branches' => fn($qb) => $qb->where('is_main', true)]);
        }])
            ->where('status', 'published')
            ->where('show_in_home', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->latest()
            ->take(10)
            ->get();

        return [
            'label' => __('home.sections.care_offers'),
            'items' => OfferResource::collection($offers)->resolve(),
        ];
    }
}
