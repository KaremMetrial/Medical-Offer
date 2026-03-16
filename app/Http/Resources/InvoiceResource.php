<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $title = $this->getInvoiceTitle();

        return [
            'id' => $this->id,
            'title' => $title,
            'date' => $this->created_at?->format('Y/m/d'),
            'amount' => (float)$this->amount,
            // Assuming we pull currency from the related country if available, or use a default
            'currency' => $this->payable?->plan?->country?->currency_symbol ?? 'د.ك',
            'amount_formatted' => $this->amount . ' ' . ($this->payable?->plan?->country?->currency_symbol ?? 'د.ك'),
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
