<?php

namespace App\Services\Payments;

use App\Models\Subscription;
use App\Models\Payment;
use App\Services\SubscriptionLifecycleService;
use App\Enums\WalletTransactionStatus;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\DB;

class PaymentCallbackHandler
{
    protected $mfService;
    protected $walletService;
    protected $subscriptionService;
    protected $subscriptionLifecycle;

    public function __construct(
        SubscriptionLifecycleService $lifecycleService, 
        MyFatoorahService $mfService,
        \App\Services\WalletService $walletService,
        \App\Services\SubscriptionService $subscriptionService
    ) {
        $this->subscriptionLifecycle = $lifecycleService;
        $this->mfService = $mfService;
        $this->walletService = $walletService;
        $this->subscriptionService = $subscriptionService;
    }




    /**
     * Handle payment success
     *
     * @param string $subscriptionId
     * @param string $providerRef (PaymentId or InvoiceId)
     * @param float $amount
     * @return array
     */
    public function handleSuccess($reference, $providerRef, $amount)
    {
        return $this->mfService->withIdempotency($providerRef, function () use ($reference, $providerRef, $amount) {
            // 1. Handle Wallet Top-up
            if (str_starts_with($reference, 'WT-')) {
                $transactionId = str_replace('WT-', '', $reference);
                $this->walletService->completeTopUp($transactionId, $providerRef, $amount);
                return ['success' => true, 'message' => 'Wallet topped up successfully'];
            }

            // 2. Handle Plan-User Reference (P{planId}U{userId})
            if (preg_match('/P(\d+)U(\d+)/', $reference, $matches)) {
                $planId = $matches[1];
                $userId = $matches[2];
                $plan = \App\Models\MemberPlan::find($planId);
                $user = \App\Models\User::find($userId);

                if ($plan && $user) {
                    $this->subscriptionService->fulfillSubscription(
                        $user,
                        $plan,
                        'online',
                        $providerRef,
                        $amount
                    );
                    return ['success' => true, 'message' => 'Subscription fulfilled successfully'];
                }
            }


            // 3. Handle Legacy Numeric Subscription ID (if any left)
            $subscription = Subscription::find($reference);

            if (!$subscription) {
                Log::error("PaymentCallbackHandler: Subscription #{$reference} not found for ref: {$providerRef}");
                return ['success' => false, 'message' => 'Subscription not found'];
            }

            if ($subscription->status === 'active' && $subscription->payment_status === 'paid') {
                return ['success' => true, 'message' => 'Already processed'];
            }

            DB::transaction(function () use ($subscription, $providerRef, $amount) {
                // 1. Activate Subscription
                $this->subscriptionLifecycle->activate($subscription);

                // 2. Update User Details (Member ID, QR Code)
                $user = $subscription->user;
                $user->update([
                    'member_id' => 'GM-' . str_pad($user->id, 4, '0', STR_PAD_LEFT) . '-' . str_pad($subscription->id, 4, '0', STR_PAD_LEFT),
                    'qr_code' => "SUB-" . $user->id . "-" . $subscription->id,
                ]);

                // 3. Record Payment
                Payment::create([
                    'payable_type' => Subscription::class,
                    'payable_id' => $subscription->id,
                    'amount' => $amount,
                    'method' => 'online',
                    'provider_ref' => $providerRef,
                    'status' => 'paid',
                ]);
            });


            Log::info("PaymentCallbackHandler: Successfully processed payment for Subscription #{$reference}");
            return ['success' => true, 'message' => 'Payment processed successfully'];
        });
    }


    /**
     * Handle payment failure
     *
     * @param string $subscriptionId
     * @param string $message
     * @return array
     */
    public function handleFailure($reference, $message)
    {
        if (str_starts_with($reference, 'WT-')) {
            $transactionId = str_replace('WT-', '', $reference);
            $transaction = \App\Models\WalletTransaction::find($transactionId);
            if ($transaction) {
                $transaction->update(['status' => WalletTransactionStatus::FAILED]);
            }

            return ['success' => false, 'message' => 'Wallet top-up failed'];
        }

        $subscription = Subscription::find($reference);
        if ($subscription) {
            $this->subscriptionLifecycle->fail($subscription, $message);
        }
        return ['success' => false, 'message' => $message];
    }


}
