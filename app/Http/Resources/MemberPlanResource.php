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

        // 1. Get Target Country (Prioritize header country_id from CountryContext)
        $countryContext = app(\App\Services\CountryContext::class);
        $targetCountry = null;

        if ($countryContext->hasCountryId()) {  
            $targetCountry = $countryContext->getCountry();
        }

        if (!$targetCountry) {
            $targetCountry = ($user && $user->country) ? $user->country : ($user && $user->country_id ? \App\Models\Country::find($user->country_id) : $countryRepo->getDefaultCountry());
        }

        $targetCurrency = $targetCountry->currency_unit ?: 'SAR';
        $factor = (float)($targetCountry->currency_factor ?: 1);
        
        // 2. Conversion using API (Prices are stored in USD as major units in DB)
        $systemBase = config('settings.currency.system_base', 'USD');
        $userLocalPrice = $currencyService->convert($this->price, $systemBase, $targetCurrency);
        $decimals = $factor == 1000 ? 3 : 2;
        $userLocalPrice = round($userLocalPrice, $decimals);

        $user = $request->user();
        $subscription = $user?->currentSubscription();

        return [
            'id' => $this->id,
            'name' => $this->name,
            'label' => $this->label,
            'features' => $this->formatFeatures($this->feature),
            'price' => (float) $userLocalPrice,
            'currency_symbol' => $targetCountry->currency_symbol,
            'price_text' => $userLocalPrice . ' ' . $targetCountry->currency_symbol . ' / ' . ($userLocalPrice * (365 / ($this->duration_days ?: 365))) . ' ' . $targetCountry->currency_symbol . ' ' . __('message.yearly'),
            'discount_label' => __('message.discounts_up_to') . ' ' . ($this->features_json['discount_percentage'] ?? 0) . '%',
            'upgrade_button_text' => __('message.upgrade_to') . ' ' . $this->name,
            'duration_days' => (int) $this->duration_days,
            'duration_text' => $this->getDurationText(),
            'is_provider' => (bool) $this->is_provider,
            'limits' => $this->features_json,
            // Card visual data
            'card_info' => [
                'user_name' => $user->name ?? '',
                'member_id' => 'GM-' . str_pad($user->id ?? 0, 4, '0', STR_PAD_LEFT) . '-' . str_pad($this->id, 4, '0', STR_PAD_LEFT),
                'expiry_date' => $subscription ? $subscription->end_at->format('Y/m/d') : now()->addYear()->format('Y/m/d'),
                'qr_code' => "SUB-" . ($user->id ?? 0) . "-" . $this->id,
                'card_title' => $this->name,
                'card_color' => $this->getCardColor($this->id),
            ]
        ];
    }

    protected function getCardColor($planId)
    {
        $colors = [
            1 => '#008AB8', // Blue
            2 => '#94772C', // Gold
            3 => '#CC5490', // Pink
        ];

        return $colors[$planId] ?? '#212529';
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
