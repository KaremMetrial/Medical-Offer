<?php

namespace App\Services\Payments;

use App\Models\User;
use App\Models\MemberPlan;
use App\Models\Country;
use Illuminate\Support\Facades\DB;

class WalletPaymentStrategy implements PaymentStrategyInterface
{
    public function process(User $user, MemberPlan $plan, array $options = []): array
    {
        $currencyService = app(\App\Services\CurrencyService::class);
        $systemBaseCurrency = config('settings.currency.system_base', 'USD');
        
        // Prices and Balances are now stored in USD (System Base)
        $priceInSystemBase = $plan->price;

        // 3. Check Balance (Balance is stored in System Base: USD)
        if ($user->balance < $priceInSystemBase) {
            return [
                'success' => false,
                'message' => __('message.insufficient_balance'),
            ];
        }

        try {
            DB::beginTransaction();

            // Deduct balance (Deduction in USD)
            $user->decrement('balance', $priceInSystemBase);

            DB::commit();

            return [
                'success' => true,
                'message' => __('message.payment_successful_wallet'),
                'transaction_id' => 'WLT-' . strtoupper(uniqid()),
                'deducted_amount' => $priceInSystemBase, // Deduction record in USD
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => __('message.payment_failed'),
            ];
        }
    }
}
