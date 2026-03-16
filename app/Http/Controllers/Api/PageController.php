<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;

class PageController extends BaseController
{
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
}
