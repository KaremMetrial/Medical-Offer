<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'plan_name' => $this->plan?->name,
            'start_at' => $this->start_at?->format('Y/m/d'),
            'end_at' => $this->end_at?->format('Y/m/d'),
            'status' => $this->status,
            'payment_status' => $this->payment_status,
            'remaining_days' => $this->remainingDays(),
            'qr_code' => $this->generateQrCode(),
            'card_info' => $this->plan ? (new MemberPlanResource($this->plan))->toArray($request)['card_info'] : null,
            'plan_details' => new MemberPlanResource($this->whenLoaded('plan')),
        ];
    }

    private function generateQrCode(): string
    {
        // Simple string for the QR code
        return "SUB-" . $this->user_id . "-" . $this->id;
    }
}
