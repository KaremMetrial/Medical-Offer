<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'name'           => $this->name,
            'email'          => $this->email,
            'phone'          => $this->phone,
            'avatar'         => $this->avatar_url,
            'role'           => $this->role,
            'is_active'      => $this->is_active,
            'country_id'     => $this->country_id,
            'governorate_id' => $this->governorate_id,
            'city_id'        => $this->city_id,
            'gender'         => $this->gender,
            'nationality_id' => $this->nationality_id,
            'balance'        => (float) $this->balance, // Base balance (SAR)
            'wallet'         => $this->getWalletInfo(),

            // Optional: Include relations if loaded
            'country'        => new JsonResource($this->whenLoaded('country')),
            'governorate'    => new JsonResource($this->whenLoaded('governorate')),
            'city'           => new JsonResource($this->whenLoaded('city')),
            'nationality'    => new JsonResource($this->whenLoaded('nationality')),

            'created_at'     => $this->created_at,
            'updated_at'     => $this->updated_at,
        ];
    }

    protected function getWalletInfo(): array
    {
        $currencyService = app(\App\Services\CurrencyService::class);
        $systemBase = config('settings.currency.system_base', 'SAR');
        
        $userCountry = $this->country ?: ($this->country_id ? \App\Models\Country::find($this->country_id) : app(\App\Repositories\Contracts\CountryRepositoryInterface::class)->getDefaultCountry());
        $userCurrency = $userCountry->currency_unit ?: 'SAR';

        $localBalance = $currencyService->convert($this->balance, $systemBase, $userCurrency);

        return [
            'local_balance' => (float) $localBalance,
            'currency_symbol' => $userCountry->currency_symbol,
            'currency_unit' => $userCurrency,
            'formatted_balance' => number_format($localBalance, 2) . ' ' . $userCountry->currency_symbol,
        ];
    }
}
