<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CardRequestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Define status steps mapping according to UI design
        $statusSteps = [
            'pending'    => 1,
            'processing' => 1,
            'prepared'   => 2,
            'shipped'    => 3,
            'delivered'  => 4,
        ];
        
        $currentStep = $statusSteps[$this->status?->value] ?? 0;

        return [
            'id'             => $this->id,
            'status'         => $this->status?->value,
            'status_label'   => $this->status?->getLabel() ?? __('message.card_status.' . $this->status?->value),
            'current_step'   => $currentStep,
            'address'        => $this->address,
            'governorate'    => $this->governorate?->name,
            'city'           => $this->city?->name,
            'receiver_name'  => $this->receiver_name,
            'receiver_phone' => $this->receiver_phone,
            'issuance_fee'   => (float)$this->issuance_fee,
            'delivery_fee'   => (float)$this->delivery_fee,
            'total_amount'   => (float)$this->total_amount,
            'created_at'     => $this->created_at?->format('Y/m/d H:i'),
        ];
    }
}
