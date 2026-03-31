<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;

use App\Repositories\Contracts\CountryRepositoryInterface;
use App\Models\Country;
use Illuminate\Http\Request;

class PageController extends BaseController
{
    public function __construct(
        protected CountryRepositoryInterface $countryRepository
    ) {}

    /**
     * Get General FAQs.
     */
    public function faqs(): JsonResponse
    {
        return $this->successResponse([
            'title' => __('pages.faq.title'),
            'items' => __('pages.faq.items'),
        ]);
    }

    /**
     * Get Plans Terms and Conditions.
     */
    public function terms(): JsonResponse
    {
        return $this->successResponse([
            'title' => __('pages.terms.title'),
            'last_update' => __('pages.terms.last_update'),
            'content' => __('pages.terms.content'),
        ]);
    }

    /**
     * Get Contact Us Information.
     */
    public function contactUs(Request $request): JsonResponse
    {
        $user = auth('sanctum')->user();
        $country = null;

        if ($user) {
            $country = $user->country;
        }

        if (!$country) {
            $country = $this->countryRepository->getDefaultCountry();
        }

        if ($country) {
            return $this->successResponse([
                'title' => $country->translation()?->contact_title ?? __('pages.contact_us.title'),
                'email' => $country->contact_email ?? __('pages.contact_us.email'),
                'phone' => $country->contact_phone ?? __('pages.contact_us.phone'),
                'whatsapp' => $country->contact_whatsapp ?? __('pages.contact_us.whatsapp'),
            ]);
        }

        return $this->successResponse([
            'title' => __('pages.contact_us.title'),
            'email' => __('pages.contact_us.email'),
            'phone' => __('pages.contact_us.phone'),
            'whatsapp' => __('pages.contact_us.whatsapp'),
        ]);
    }
}

