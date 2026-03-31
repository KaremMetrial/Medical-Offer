<?php

namespace App\Services;

use App\Models\User;
use App\Models\MemberPlan;
use App\Models\Subscription;
use App\Models\Payment;
use App\Enums\PaymentMethod;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SubscriptionService
{
    public function __construct(
        protected \App\Repositories\Contracts\SubscriptionRepositoryInterface $subscriptionRepository,
        protected \App\Services\SubscriptionLifecycleService $lifecycleService
    ) {}

    /**
     * Complete the full subscription process (Atomic)
     * 
     * @param User $user
     * @param MemberPlan $plan
     * @param string $paymentMethod ('wallet', 'online', 'bank')
     * @param string|null $providerRef
     * @param float $amount
     * @return Subscription
     */
    public function fulfillSubscription(User $user, MemberPlan $plan, string $paymentMethod, ?string $providerRef, float $amount): Subscription
    {
        return DB::transaction(function () use ($user, $plan, $paymentMethod, $providerRef, $amount) {
            // 1. Create or Update the Subscription record
            $subscription = $this->subscriptionRepository->createSubscription([
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'status' => 'active', // Direct fulfillment means it's active
                'payment_status' => 'paid',
                'start_at' => now(),
                'end_at' => now()->addDays($plan->duration_days),
            ]);

            // 2. Update User Identification Details
            $user->update([
                'member_id' => 'GM-' . str_pad($user->id, 4, '0', STR_PAD_LEFT) . '-' . str_pad($subscription->id, 4, '0', STR_PAD_LEFT),
                'qr_code' => "SUB-" . $user->id . "-" . $subscription->id,
            ]);

            // 3. Create the payment record for audit trail
            Payment::create([
                'payable_type' => Subscription::class,
                'payable_id' => $subscription->id,
                'amount' => $amount,
                'method' => $paymentMethod,
                'provider_ref' => $providerRef,
                'status' => 'paid',
            ]);

            Log::info("Subscription fulfilled successfully for User #{$user->id} with Plan #{$plan->id}");

            return $subscription;
        });
    }
}
