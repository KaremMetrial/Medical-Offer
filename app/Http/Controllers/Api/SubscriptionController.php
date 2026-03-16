<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\SubscriptionResource;
use App\Http\Resources\InvoiceResource;
use App\Repositories\Contracts\SubscriptionRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubscriptionController extends BaseController
{
    protected $subscriptionRepository;

    public function __construct(SubscriptionRepositoryInterface $subscriptionRepository)
    {
        $this->subscriptionRepository = $subscriptionRepository;
    }

    /**
     * Get user's active subscription dashboard data.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $subscription = $this->subscriptionRepository->getUserSubscription($user->id);

        return $this->successResponse([
            'subscription' => $subscription ? new SubscriptionResource($subscription) : null,
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
}
