<?php

namespace App\Repositories\Eloquent;

use App\Models\Subscription;
use App\Models\Payment;
use App\Repositories\Contracts\SubscriptionRepositoryInterface;

class SubscriptionRepository extends BaseRepository implements SubscriptionRepositoryInterface
{
    public function __construct(Subscription $model)
    {
        parent::__construct($model);
    }

    public function getUserSubscription($userId, array $relations = ['plan.translations'])
    {
        return $this->model->with($relations)
            ->where('user_id', $userId)
            ->where('status', 'active')
            ->where('payment_status', 'paid')
            ->orderBy('created_at', 'desc')
            ->first();
    }

    public function getUserInvoices($userId)
    {
        return Payment::where('payable_type', Subscription::class)
            ->with('payable.plan.translations')
            ->whereHasMorph('payable', [Subscription::class], function($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function createSubscription(array $data)
    {
        $plan = \App\Models\MemberPlan::findOrFail($data['plan_id']);
        
        return $this->model->create([
            'user_id' => $data['user_id'],
            'plan_id' => $data['plan_id'],
            'start_at' => now(),
            'end_at' => now()->addDays($plan->duration_days),
            'status' => $data['status'] ?? 'pending',
            'payment_status' => $data['payment_status'] ?? 'unpaid',
        ]);
    }

    public function activateSubscription($subscriptionId, $transactionId = null)
    {
        $subscription = $this->model->findOrFail($subscriptionId);
        $subscription->update([
            'status' => 'active',
            'payment_status' => 'paid',
        ]);

        // Create payment record
        Payment::create([
            'payable_type' => Subscription::class,
            'payable_id' => $subscription->id,
            'amount' => $subscription->plan->price,
            'method' => 'wallet', // Or dynamic
            'provider_ref' => $transactionId,
            'status' => 'paid',
        ]);

        return $subscription;
    }
}
