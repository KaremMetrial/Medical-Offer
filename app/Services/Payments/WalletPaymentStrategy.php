<?php

namespace App\Services\Payments;

use App\Models\User;
use App\Models\MemberPlan;
use App\Models\Country;
use Illuminate\Support\Facades\DB;

class WalletPaymentStrategy implements PaymentStrategyInterface
{
    public function process(User $user, MemberPlan $plan): array
    {
        $currencyService = app(\App\Services\CurrencyService::class);
        $systemBaseCurrency = config('settings.currency.system_base', 'EGP');
        
        // 1. Get Currencies
        $planCountry = $plan->country ?: ($plan->country_id ? \App\Models\Country::find($plan->country_id) : app(\App\Repositories\Contracts\CountryRepositoryInterface::class)->getDefaultCountry());
        $planCurrency = $planCountry->currency_unit ?: 'SAR';

        // 2. Convert Plan Price to System Base Currency (SAR)
        // If users pay in SAR, we need to know how much SAR the plan costs
        $priceInSystemBase = $currencyService->convert($plan->price, $planCurrency, $systemBaseCurrency);

        // 3. Check Balance (Balance is stored in System Base: SAR)
        if ($user->balance < $priceInSystemBase) {
            return [
                'success' => false,
                'message' => __('message.insufficient_balance'),
            ];
        }

        try {
            DB::beginTransaction();

            // Deduct balance (Deduction in SAR)
            $user->decrement('balance', $priceInSystemBase);

            DB::commit();

            return [
                'success' => true,
                'message' => __('message.payment_successful_wallet'),
                'transaction_id' => 'WLT-' . strtoupper(uniqid()),
                'deducted_amount' => $priceInSystemBase, // Deduction record in SAR
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
