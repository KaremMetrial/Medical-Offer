<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\CardRequestResource;
use App\Http\Resources\GovernorateResource;
use App\Services\CardRequestService;
use App\Models\Governorate;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\StoreCardRequest;

class CardRequestController extends BaseController
{
    public function __construct(protected CardRequestService $service){}

    /**
     * Initialize the card request form with metadata and fees.
     */
    public function init(Request $request): JsonResponse
    {
        $user = $request->user();
        $subscription = $user?->currentSubscription();
        $fees = $this->service->getFees($user);
        
        $countryId = $user->country_id ?? $request->header('x-country-id') ?? config('settings.default_country_id');
        $governorates = Governorate::with(['cities.translations', 'translations'])
            ->where('country_id', $countryId)
            ->where('is_active', true)
            ->get();
            
        return $this->successResponse([
            'fees' => $fees,
            'card_info' => $this->getInfoCard($user, $subscription),
            'governorates' => GovernorateResource::collection($governorates),
            'labels' => [
                'card_info' => __('message.card_info'),
                'governorates' => __('message.governorates'),
                'fees' => __('message.fees'),
                'card_request' => __('message.card_request'),
                'issuance_fee' => __('message.issuance_fee'),
                'delivery_fee' => __('message.delivery_fee'),
                'total_amount' => __('message.total_amount'),
                'address' => __('message.address'),
                'governorate' => __('message.governorate'),
                'city' => __('message.city'),
                'receiver_name' => __('message.receiver_name'),
                'receiver_phone' => __('message.receiver_phone'),
                'submit_request' => __('message.submit_request'),
            ],
        ]);
    }
    protected function getInfoCard($user, $subscription)
    {
        return [
            'user_name' => $user->name ?? '',
            'member_id' => 'GM-' . str_pad($user->id ?? 0, 4, '0', STR_PAD_LEFT) . '-' . str_pad($subscription?->id ?? 0, 4, '0', STR_PAD_LEFT),
            'expiry_date' => $subscription ? $subscription->end_at->format('Y/m/d') : now()->addYear()->format('Y/m/d'),
            'qr_code' => "SUB-" . ($user->id ?? 0) . "-" . ($subscription?->id ?? 0),
            'card_title' => $subscription?->plan?->name ?? 'Default Plan',
            'card_color' => $this->getCardColor($subscription?->plan?->id),
            'plan_label' => $subscription?->plan?->label ?? '',
        ];
    }
    protected function getCardColor($planId)
    {
        $colors = [
            1 => '#008AB8', // Blue
            2 => '#94772C', // Gold
            3 => '#CC5490', // Pink
        ];

        return $colors[$planId] ?? '#212529';
    }
    /**
     * Submit a new card request.
     */
    public function store(StoreCardRequest $request): JsonResponse
    {
        $cardRequest = $this->service->createRequest($request->user(), $request->validated());

        return $this->successResponse(
            new CardRequestResource($cardRequest),
            __('message.card_request_submitted_successfully')
        );
    }


    /**
     * Get the status of the latest card request (for tracking).
     */
    public function status(Request $request): JsonResponse
    {
        $latestRequest = $this->service->getLatestRequest($request->user());

        if (!$latestRequest) {
            return $this->errorResponse(__('message.no_card_request_found'), 404);
        }

        return $this->successResponse([
            'card_info' => $this->getInfoCard($request->user(), $request->user()->currentSubscription()),
            'card_request' => new CardRequestResource($latestRequest),
            'labels' => [
                'card_info' => __('message.card_info'),
                'card_request' => __('message.card_request'),
                'receiver_name' => __('message.receiver_name'),
                'receiver_phone' => __('message.receiver_phone'),
                'issuance_fee' => __('message.issuance_fee'),
                'delivery_fee' => __('message.delivery_fee'),
                'total_amount' => __('message.total_amount'),
                'created_at' => __('message.created_at'),
                'status' => __('message.status'),
            ],
        ]);
    }
}
