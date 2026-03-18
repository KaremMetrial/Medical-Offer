<?php

namespace App\Services\Payments;

use App\Models\User;
use App\Models\MemberPlan;
use App\Models\Country; // Added for explicit Country model usage

class OnlinePaymentStrategy implements PaymentStrategyInterface
{
    public function process(User $user, MemberPlan $plan): array
    {
        $countryRepo = app(\App\Repositories\Contracts\CountryRepositoryInterface::class);

        $currencyService = app(\App\Services\CurrencyService::class);
        $planCountry = $plan->country ?: ($plan->country_id ? Country::find($plan->country_id) : $countryRepo->getDefaultCountry());
        $userCountry = $user->country ?: ($user->country_id ? Country::find($user->country_id) : $countryRepo->getDefaultCountry());
        
        $userCurrency = $userCountry->currency_unit ?: 'SAR';
        $systemBase = config('settings.currency.system_base', 'USD');
        $effectivePriceInUserCurrency = $currencyService->convert($plan->price, $systemBase, $userCurrency);

        // Integration with external gateway would happen here
        return [
            'success' => true,
            'message' => __('message.payment_redirect_online'),
            'transaction_id' => null, 
            'redirect_url' => 'https://payment-gateway.com/pay?amount=' . round($effectivePriceInUserCurrency, 2) . '&currency=' . $userCurrency,
        ];
    }
}
