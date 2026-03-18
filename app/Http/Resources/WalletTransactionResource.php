<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WalletTransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $countryContext = app(\App\Services\CountryContext::class);
        $country = $countryContext->getCountry();
        
        if (!$country) {
            $user = $request->user();
            $country = $user?->country;
        }

        $currencyService = app(\App\Services\CurrencyService::class);
        $systemBase = config('settings.currency.system_base', 'USD');
        $unit = $country?->currency_unit ?? 'USD';
        $factor = (float)($country?->currency_factor ?: 1);
        $decimals = $factor == 1000 ? 3 : 2;

        return [
            'id'           => $this->id,
            'type'         => $this->type,
            'amount'       => round($currencyService->convert((float) $this->amount, $systemBase, $unit), $decimals),
            'balance_after' => round($currencyService->convert((float) $this->balance_after, $systemBase, $unit), $decimals),
            'description'  => $this->description,
            'reference'    => $this->reference,
            'created_at'   => $this->created_at?->format('Y/m/d H:i'),
            'currency_symbol' => $unit,
        ];
    }
}
