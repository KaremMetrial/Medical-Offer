<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\MemberPlanResource;
use App\Repositories\Contracts\MemberPlanRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MemberPlanController extends BaseController
{
    protected $memberPlanRepository;

    public function __construct(MemberPlanRepositoryInterface $memberPlanRepository)
    {
        $this->memberPlanRepository = $memberPlanRepository;
    }

    public function index(Request $request): JsonResponse
    {
        $countryId = $request->get('country_id');
        $plans = $this->memberPlanRepository->getActivePlans($countryId);

        return $this->successResponse([
            'plans' => MemberPlanResource::collection($plans),
            'labels' => [
                'title' => __('message.subscription_plans'),
                'how_to_use' => __('message.how_to_use_discount'),
                'steps' => [
                    __('message.step_1'),
                    __('message.step_2'),
                    __('message.step_3'),
                ],
            ]
        ]);
    }
    public function show($id): JsonResponse
    {
        $plan = $this->memberPlanRepository->findOrFail($id, ['*'], ['translations', 'country']);

        return $this->successResponse([
            'plan' => new MemberPlanResource($plan),
            'features_details' => $this->getFeaturesDetails($plan),
            'faqs' => $this->getFaqs(),
            'labels' => $this->getLabels(),
        ]);
    }
    private function getFeaturesDetails($plan): array
    {
        return [
            [
                'label' => __('message.number_of_companions'),
                'value' => ($plan->features_json['number_of_buddies'] ?? 0) . ' ' . __('message.companions'),
                'icon' => asset('storage/users.png')
            ],
            [
                'label' => __('message.medical_network'),
                'value' => ($plan->features_json['number_of_providers'] ?? 0) . ' ' . __('message.medical_network'),
                'icon' => asset('storage/hospital.png')
            ],
            [
                'label' => __('message.visits_count'),
                'value' => ($plan->features_json['number_of_visits'] ?? 0) . ' ' . __('message.visits_count'),
                'icon' => asset('storage/visits.png')
            ],
            [
                'label' => __('message.discount_percentage'),
                'value' => ($plan->features_json['discount_percentage'] ?? 0) . ' ' . __('message.discount_percentage'),
                'icon' => asset('storage/discount.png')
            ],
        ];
    }
    private function getFaqs(): array
    {
        return [
            [
                'question' => __('message.faq_1_q'),
                'answer' => __('message.faq_1_a'),
            ],
            [
                'question' => __('message.faq_2_q'),
                'answer' => __('message.faq_2_a'),
            ],
            [
                'question' => __('message.faq_3_q'),
                'answer' => __('message.faq_3_a'),
            ],
        ];
    }
    private function getLabels(): array
    {
        return [
            'title' => __('message.subscription_details'),
            'features_title' => __('message.features'),
            'why_choose_title' => __('message.why_choose_plan'),
            'best_seller' => __('message.best_seller'),
            'yearly_plan' => __('message.yearly_plan'),
            'subscribe_now' => __('message.subscribe_now'),
            'faq_title' => __('message.faq_title'),
        ];
    }
}
