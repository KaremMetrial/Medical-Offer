<?php

namespace App\Services;

use App\Models\Subscription;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SubscriptionLifecycleService
{
    /**
     * Activate a subscription
     *
     * @param Subscription $subscription
     * @param Payment|null $payment
     * @return Subscription
     */
    public function activate(Subscription $subscription, Payment $payment = null)
    {
        return DB::transaction(function () use ($subscription, $payment) {
            $subscription->update([
                'status' => 'active',
                'payment_status' => 'paid',
                'start_at' => now(),
                'end_at' => now()->addYear(), // Annual membership
            ]);

            if ($payment) {
                $payment->update(['status' => 'paid']);
            }

            // TODO: Trigger events/notifications
            // event(new SubscriptionActivated($subscription));

            return $subscription;
        });
    }

    /**
     * Cancel a subscription
     *
     * @param Subscription $subscription
     * @return Subscription
     */
    public function cancel(Subscription $subscription)
    {
        $subscription->update(['status' => 'canceled']);
        return $subscription;
    }


    /**
     * Mark subscription as expired
     *
     * @param Subscription $subscription
     * @return Subscription
     */
    public function expire(Subscription $subscription)
    {
        $subscription->update(['status' => 'expired']);
        return $subscription;
    }

    /**
     * Mark subscription as failed
     *
     * @param Subscription $subscription
     * @param string $message
     * @return Subscription
     */
    public function fail(Subscription $subscription, $message = '')
    {
        $subscription->update(['payment_status' => 'unpaid']);
        Log::warning("Subscription #{$subscription->id} payment failed: {$message}");
        return $subscription;
    }


    /**
     * Auto-cancel expired pending subscriptions
     * (e.g., those that haven't been paid within the window)
     */
    public function cleanUpPendingSubscriptions($minutes = 15)
    {
        $expiredSubscriptions = Subscription::where('status', 'pending')
            ->where('created_at', '<', now()->subMinutes($minutes))
            ->get();

        foreach ($expiredSubscriptions as $subscription) {
            $this->cancel($subscription);
            Log::info("Auto-cancelled pending subscription #{$subscription->id} due to timeout.");
        }

        return $expiredSubscriptions->count();
    }
}

