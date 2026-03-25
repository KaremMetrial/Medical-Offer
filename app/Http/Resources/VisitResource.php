<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VisitResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $countryContext = app(\App\Services\CountryContext::class);
        $currencyService = app(\App\Services\CurrencyService::class);
        
        $targetCountry = $countryContext->getCountry() ?? app(\App\Repositories\Contracts\CountryRepositoryInterface::class)->getDefaultCountry();
        $targetCurrency = $targetCountry->currency_unit ?? 'EGP';
        
        // Base currency of the visit is the provider's country currency
        $sourceCurrency = $this->provider?->country?->currency_unit ?? 'EGP';
        
        $factor = (float)($targetCountry->currency_factor ?: 1);
        $decimals = $factor == 1000 ? 3 : 2;
        
        $paidAmount = $currencyService->convert((float)$this->paid_amount, $sourceCurrency, $targetCurrency);
        $discountAmount = $currencyService->convert((float)$this->discount_amount, $sourceCurrency, $targetCurrency);
        
        return [
            'id' => $this->id,
            'provider' => ProviderResource::make($this->whenLoaded('provider')),
            'companion' => UserResource::make($this->whenLoaded('companion')),
            'visit_date' => $this->visit_date ? $this->visit_date->format('Y-m-d H:i:s') : null,
            'services' => collect($this->services)->map(function($service) use ($currencyService, $sourceCurrency, $targetCurrency, $decimals) {
                $serviceDiscount = (float)(is_array($service) ? ($service['discount_amount'] ?? 0) : 0);
                $convertedDiscount = $currencyService->convert($serviceDiscount, $sourceCurrency, $targetCurrency);
                
                return [
                    'name' => is_array($service) ? ($service['name'] ?? reset($service)) : $service,
                    'description' => is_array($service) ? ($service['description'] ?? '') : '',
                    'discount_amount' => round($convertedDiscount, $decimals),
                ];
            }),
            'paid_amount' => round($paidAmount, $decimals),
            'discount_amount' => round($discountAmount, $decimals),
            'currency_symbol' => $targetCountry->currency_symbol ?? __('message.currency'),
            'status' => $this->status,
            'created_at' => $this->created_at ? $this->created_at->format('Y-m-d H:i:s') : null,
        ];
    }
}
