<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WithdrawalResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $currencyService = app(\App\Services\CurrencyService::class);
        $systemBase = config('settings.currency.system_base', 'USD');
        
        // Get the country from context (header) or fallback to user's country
        $countryContext = app(\App\Services\CountryContext::class);
        $country = $countryContext->getCountry();
        
        if (!$country) {
            $country = $this->user->country;
        }

        $unit = $country?->currency_unit ?? 'EGP';
        $factor = (float)($country?->currency_factor ?: 1);
        $decimals = $factor == 1000 ? 3 : 2;


        return [
            'id' => $this->id,
            'amount' => (float) round($currencyService->convert((float)$this->amount, $systemBase, $unit), $decimals),
            'fee' => (float) round($currencyService->convert((float)$this->fee, $systemBase, $unit), $decimals),
            'net_amount' => (float) round($currencyService->convert((float)$this->net_amount, $systemBase, $unit), $decimals),
            'currency' => $unit,
            'status' => $this->status->value,
            'status_label' => $this->status->getLabel(),
            'method' => $this->method,
            'payment_details' => $this->payment_details,
            'rejection_reason' => $this->rejection_reason,
            'reference_id' => $this->reference_id,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }

}
