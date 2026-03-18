<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $title = $this->getInvoiceTitle();

        $targetCountry = $this->payable?->plan?->country;
        $targetCurrency = $targetCountry?->currency_unit ?: 'KWD';
        $factor = (float)($targetCountry?->currency_factor ?: 1000);
        $decimals = $factor == 1000 ? 3 : 2;
        
        $currencyService = app(\App\Services\CurrencyService::class);
        $systemBase = config('settings.currency.system_base', 'USD');
        $localAmount = $currencyService->convert((float)$this->amount, $systemBase, $targetCurrency);
        $localAmountRounded = round($localAmount, $decimals);
        $symbol = $targetCountry?->currency_symbol ?? 'د.ك';

        return [
            'id' => $this->id,
            'title' => $title,
            'date' => $this->created_at?->format('Y/m/d'),
            'amount' => $localAmountRounded,
            'currency_symbol' => $symbol,
            'amount_formatted' => $localAmountRounded . ' ' . $symbol,
            'status' => $this->status,
            'status_label' => $this->status === 'paid' ? __('message.paid') : __('message.unpaid'),
            'reference' => $this->provider_ref,
            'view_invoice_label' => __('message.view_invoice'),
        ];
    }

    private function getInvoiceTitle(): string
    {
        $planName = $this->payable?->plan?->name ?? __('message.subscription');
        $duration = $this->payable?->plan?->duration_text ?? '';
        
        return __('message.subscription_invoice', [
            'plan' => $planName,
            'duration' => $duration
        ]);
    }
}
