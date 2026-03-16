<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MemberPlanResource extends JsonResource
{
    public function toArray($request)
    {
        $countryRepo = app(\App\Repositories\Contracts\CountryRepositoryInterface::class);
        $currencyService = app(\App\Services\CurrencyService::class);
        $user = $request->user();

        // 1. Get Countries & Currencies
        $userCountry = ($user && $user->country) ? $user->country : ($user && $user->country_id ? \App\Models\Country::find($user->country_id) : $countryRepo->getDefaultCountry());
        $planCountry = $this->country ?: ($this->country_id ? \App\Models\Country::find($this->country_id) : $countryRepo->getDefaultCountry());
        
        $userCurrency = $userCountry->currency_unit ?: 'SAR';
        $planCurrency = $planCountry->currency_unit ?: 'SAR';

        // 2. Conversion using API
        $userLocalPrice = $currencyService->convert($this->price, $planCurrency, $userCurrency);

        return [
            'id' => $this->id,
            'name' => $this->name,
            'label' => $this->label,
            'features' => $this->formatFeatures($this->feature),
            'price' => (float) $this->price,
            'user_price' => round($userLocalPrice, 2),
            'user_currency_symbol' => $userCountry->currency_symbol,
            'duration_days' => (int) $this->duration_days,
            'duration_text' => $this->getDurationText(),
            'is_provider' => (bool) $this->is_provider,
            'limits' => $this->features_json,
            'currency_symbol' => $this->country?->currency_symbol ?? '$',
            'currency_unit' => $this->country?->currency_unit ?? 'USD',
            'currency_factor' => (int) $this->country?->currency_factor ?? 1,
        ];
    }

    protected function getDurationText()
    {
        if ($this->duration_days >= 365) {
            return __('message.yearly');
        } elseif ($this->duration_days >= 30) {
            return __('message.monthly');
        }
        
        return $this->duration_days . ' ' . __('message.days');
    }

    protected function formatFeatures($feature)
    {
        if (empty($feature)) {
            return [];
        }

        $lines = explode("\n", $feature);

        return collect($lines)
            ->map(fn($line) => trim(preg_replace('/^(\*|-|\d+\.|\d+\))\s+/', '', trim($line))))
            ->filter()
            ->values()
            ->all();
    }
}
