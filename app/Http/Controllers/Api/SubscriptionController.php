<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\SubscriptionResource;
use App\Http\Resources\InvoiceResource;
use App\Http\Resources\CompanionResource;
use App\Repositories\Contracts\{
    SubscriptionRepositoryInterface,
    MemberPlanRepositoryInterface,
    UserRepositoryInterface
};
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\CreateCompanion;

class SubscriptionController extends BaseController
{

    public function __construct(
        protected SubscriptionRepositoryInterface $subscriptionRepository,
        protected MemberPlanRepositoryInterface $memberPlanRepository,
        protected UserRepositoryInterface $userRepository
    ) {
    }

    /**
     * Get user's active subscription dashboard data.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user()->load('children');
        $subscription = $user->currentSubscription();

        return $this->successResponse([
            'subscription' => $subscription ? new SubscriptionResource($subscription) : null,
            'summary' => $this->getSubscriptionSummary($user, $subscription),
            'companions' => $this->getCompanions($user),
            'quick_actions' => $this->getQuickActions(),
            'is_has_active_card_request' => $user->cardRequests()->whereNotIn('status', ['delivered', 'cancelled'])->exists(),
            'management' => $this->getManagement(),
            'labels' => [
                'title' => __('message.my_subscription'),
                'no_subscription' => __('message.no_active_subscription'),
                'subscribe_now' => __('message.subscribe_now'),
                'expiry_date' => __('message.expiry_date'),
                'remaining_days' => __('message.remaining_days'),
                'show_qr' => __('message.show_qr_code'),
            ]
        ]);
    }
    private function getManagement()
    {
        return [
            'title' => __('message.management_title'),
            'links' => [
                [
                    'title' => __('message.invoice_history'),
                    'subtitle' => __('message.invoice_history'),
                    'icon' => asset('storage/invoice.png'),
                    'route' => 'invoices',
                ],
                [
                    'title' => __('message.terms_conditions'),
                    'subtitle' => __('message.view_policy'),
                    'icon' => asset('storage/terms.png'),
                    'route' => 'terms',
                ],
                [
                    'title' => __('message.faq_title'),
                    'subtitle' => __('message.faq_desc'),
                    'icon' => asset('storage/faq.png'),
                    'route' => 'faq',
                ],
            ]
        ];
    }
    private function getQuickActions()
    {
        return [
            [
                'title' => __('message.request_card'),
                'subtitle' => __('message.membership_card_request'),
                'icon' => asset('storage/card_request.png'),
                'color' => '#CC5490',
            ],
            [
                'title' => __('message.upgrade_your_subscription'),
                'subtitle' => __('message.upgrade_subscription_desc'),
                'icon' => asset('storage/upgrade_rocket.png'),
                'color' => '#00B4FF',
            ],
        ];
    }
    private function getCompanions($user)
    {
        $subscription = $user->currentSubscription();
        $maxBuddies = $subscription ? (int)($subscription->plan->features_json['number_of_buddies'] ?? 0) : 0;
        $currentCount = $user->children()->count();

        return [
            'title' => __('message.companions'),
            'add_label' => __('message.add_companion'),
            'limit' => $maxBuddies,
            'current_count' => $currentCount,
            'can_add' => ($currentCount < $maxBuddies),
            'list' => CompanionResource::collection($user->children),
        ];
    }
    private function getSubscriptionSummary($user, $subscription)
    {
        return [
            'title' => __('message.subscription_summary'),
            'items' => [
                [
                    'label' => __('message.total_savings'),
                    'value' => '50 ' . ($user->country?->currency_symbol ?? 'د.ك'),
                    'icon' => asset('storage/savings.png'),
                ],
                [
                    'label' => __('message.visits_used'),
                    'value' => '50 ' . __('message.visits_count'),
                    'icon' => asset('storage/visits_checked.png'),
                ],
                [
                    'label' => __('message.renewal_date'),
                    'value' => $subscription ? $subscription->end_at->format('Y/m/d') : now()->format('Y/m/d'),
                    'icon' => asset('storage/renewal.png'),
                ],
            ]
        ];
    }
    /**
     * Get user's upgrade plans.
     */
    public function upgratePlans(Request $request): JsonResponse
    {
        $user = $request->user();
        $subscription = $user->currentSubscription();
        
        $countryId = $request->get('country_id');
        $currentPlanId = $subscription ? $subscription->plan_id : null;
        $plans = $this->memberPlanRepository->getActivePlans($countryId);
        
        // Filter out current plan and format available plans
        $availablePlans = $plans->where('id', '!=', $currentPlanId);

        return $this->successResponse([
            'current_subscription' => $subscription ? new \App\Http\Resources\MemberPlanResource($subscription->plan) : null,
            'available_plans' => \App\Http\Resources\MemberPlanResource::collection($availablePlans),
            'labels' => [
                'title' => __('message.upgrade_your_subscription'),
                'current_plan_header' => __('message.your_current_plan'),
                'available_plans_header' => __('message.available_plans'),
                'features_header' => __('message.features'),
            ]
        ]);
    }

    /**
     * Get user's invoice history.
     */
    public function invoices(Request $request): JsonResponse
    {
        $user = $request->user();
        $invoices = $this->subscriptionRepository->getUserInvoices($user->id);

        return $this->successResponse([
            'invoices' => InvoiceResource::collection($invoices),
            'labels' => [
                'title' => __('message.invoice_history'),
                'invoice_date' => __('message.invoice_date'),
                'payment_amount' => __('message.payment_amount'),
                'payment_status' => __('message.payment_status'),
                'payment_ref' => __('message.payment_ref'),
            ]
        ]);
    }

    /**
     * Subscribe to a plan.
     */
    public function subscribe(Request $request): JsonResponse
    {
        $request->validate([
            'plan_id' => 'required|exists:member_plans,id',
            'payment_method' => 'required|in:' . implode(',', \App\Enums\PaymentMethod::values()),
        ]);

        $user = $request->user();
        $plan = \App\Models\MemberPlan::findOrFail($request->plan_id);
        $method = \App\Enums\PaymentMethod::from($request->payment_method);

        // 1. Get Payment Strategy
        $paymentStrategy = \App\Services\Payments\PaymentFactory::make($method);

        // 2. Process Payment (Check balance happens inside Wallet strategy)
        $paymentResult = $paymentStrategy->process($user, $plan);

        if (!$paymentResult['success']) {
            return $this->errorResponse($paymentResult['message'], 400);
        }

        // 3. Create Subscription
        // If wallet, it's already "paid" and "active"
        $status = ($method === \App\Enums\PaymentMethod::WALLET) ? 'active' : 'pending';
        $paymentStatus = ($method === \App\Enums\PaymentMethod::WALLET) ? 'paid' : 'unpaid';

        $subscription = $this->subscriptionRepository->createSubscription([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'status' => $status,
            'payment_status' => $paymentStatus,
        ]);

        // 4. Create Payment Record if success
        if ($paymentResult['transaction_id']) {
            \App\Models\Payment::create([
                'payable_type' => \App\Models\Subscription::class,
                'payable_id' => $subscription->id,
                'amount' => $paymentResult['deducted_amount'] ?? $plan->price,
                'method' => $method->value,
                'provider_ref' => $paymentResult['transaction_id'],
                'status' => 'paid',
            ]);
        }

        return $this->successResponse([
            'subscription' => new SubscriptionResource($subscription),
            'message' => $paymentResult['message'],
            'redirect_url' => $paymentResult['redirect_url'] ?? null,
        ]);
    }

    public function addCompanion(CreateCompanion $request): JsonResponse
    {
        $user = $request->user();
        $validated = $request->validated();
        $validated['parent_user_id'] = $user->id;
        $validated['role'] = 'user';
        $validated['is_active'] = true;
        
        $companion = $this->userRepository->create($validated);

        if ($request->has('attachments')) {
            foreach ($request->attachments as $attachmentData) {
                $path = $attachmentData['file']->store('users/attachments', 'public');
                $companion->attachments()->create([
                    'path' => $path,
                    'type' => $attachmentData['type'],
                    'file_type' => $attachmentData['file']->getClientOriginalExtension(),
                ]);
            }
        }

        return $this->successResponse([
            'companion' => new CompanionResource($companion),
            'message' => __('message.companion_added_successfully'),
        ]);
    }
}
