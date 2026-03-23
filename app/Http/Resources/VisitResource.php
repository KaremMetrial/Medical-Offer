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
        return [
            'id' => $this->id,
            'provider' => new ProviderResource($this->whenLoaded('provider')),
            'companion' => new UserResource($this->whenLoaded('companion')),
            'visit_date' => $this->visit_date ? $this->visit_date->format('Y-m-d H:i:s') : null,
            'services' => $this->services,
            'paid_amount' => $this->paid_amount,
            'discount_amount' => $this->discount_amount,
            'status' => $this->status,
            'created_at' => $this->created_at ? $this->created_at->format('Y-m-d H:i:s') : null,
        ];
    }
}
