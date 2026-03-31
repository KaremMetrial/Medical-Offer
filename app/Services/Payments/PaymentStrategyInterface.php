<?php

namespace App\Services\Payments;

use App\Models\User;
use App\Models\MemberPlan;

interface PaymentStrategyInterface
{
    /**
     * @param User $user
     * @param MemberPlan $plan
     * @param array $options
     * @return array ['success' => bool, 'message' => string, 'transaction_id' => string|null]
     */
    public function process(User $user, MemberPlan $plan, array $options = []): array;
}
