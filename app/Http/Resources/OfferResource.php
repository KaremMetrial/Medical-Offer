<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OfferResource extends JsonResource
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
            'name' => $this->name,
            'description' => $this->description,
            'discount_percent' => $this->discount_percent,
            'image' => $this->images->where('type', 'image')->first()?->src ?? $this->provider?->logo_url,
            'rating' => round($this->reviews()->avg('rating') ?? 0, 1),
            'reviews_count' => $this->reviews()->count(),
            'provider_name' => $this->provider?->name,
            'provider_logo' => $this->provider?->logo_url,
            'address' => $this->provider?->mainBranch()?->address ?? $this->provider?->country?->name,
        ];
    }
}
