<?php

namespace App\Services\Payments;

use App\Models\User;
use App\Models\MemberPlan;
use App\Models\Country;
use Illuminate\Support\Facades\Log;

class OnlinePaymentStrategy implements PaymentStrategyInterface
{
    protected $mfService;

    public function __construct(MyFatoorahService $mfService)
    {
        $this->mfService = $mfService;
    }

    public function process(User $user, MemberPlan $plan, array $options = []): array
    {
        $countryContext = app(\App\Services\CountryContext::class);
        $countryRepo = app(\App\Repositories\Contracts\CountryRepositoryInterface::class);
        $currencyService = app(\App\Services\CurrencyService::class);

        $userCountry = $countryContext->getCountry() ?:
            $user->country ?: ($user->country_id ? Country::find($user->country_id) : $countryRepo->getDefaultCountry());

        // Force EGP for MyFatoorah display as requested by user
        $userCurrency = 'EGP';
        $planPriceSource = config('settings.currency.base', 'USD');

        Log::info("Payment Conversion (Force USD Base):", [
            'plan_id' => $plan->id,
            'raw_price' => $plan->price,
            'source_currency' => $planPriceSource,
            'target_currency' => $userCurrency
        ]);

        $effectivePrice = $currencyService->convert($plan->price, $planPriceSource, $userCurrency);

        Log::info("Payment Conversion:", [
            'plan_price' => $plan->price,
            'source_currency' => $planPriceSource,
            'user_currency' => $userCurrency,
            'effective_price' => $effectivePrice,
            'user_id' => $user->id,
            'plan_id' => $plan->id
        ]);

        try {
            $callbackURL = route('myfatoorah.callback');
            $planId = $options['plan_id'] ?? $plan->id;
            $customerReference = "P{$planId}U{$user->id}";

            $curlData = [
                'CustomerName'       => $user->name ?: 'Customer',
                'InvoiceValue'       => round($effectivePrice, 2),
                'DisplayCurrencyIso' => $userCurrency,
                'CustomerEmail'      => $user->email ?: 'test@test.com',
                'CallBackUrl'        => $callbackURL,
                'ErrorUrl'           => $callbackURL,
                'Language'           => app()->getLocale(),
                'CustomerReference'  => $customerReference,
                'SourceInfo'         => config('app.name') . ' ' . app()::VERSION . ' - MyFatoorah Integration',
            ];


            $data = $this->mfService->createInvoice($curlData);
            
            // Redirect through our local gateway to show our domain
            $gatewayUrl = route('myfatoorah.gateway', ['url' => $data['invoiceURL']]);

            return [
                'success'      => true,
                'message'      => __('message.payment_redirect_online'),
                'transaction_id' => null,
                'redirect_url' => $gatewayUrl,
                'payment_id'   => $data['invoiceId'],
            ];

        } catch (\Exception $ex) {
            Log::error('OnlinePaymentStrategy: ' . $ex->getMessage());
            return [
                'success' => false,
                'message' => $ex->getMessage(),
            ];
        }
    }
}
