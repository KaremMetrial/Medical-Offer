<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProviderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Get max discount using already loaded offers relationship if available
        $maxDiscount = 0;
        if ($this->relationLoaded('offers')) {
            $maxDiscount = $this->offers->max('discount_percent') ?? 0;
        } else {
            // Fall back to query if offers not loaded
            $maxDiscount = $this->offers()->max('discount_percent') ?? 0;
        }

        // Get main branch address using already loaded branches relationship if available
        $address = $this->country?->name;
        if ($this->relationLoaded('branches')) {
            $mainBranch = $this->branches->firstWhere('is_main', true);
            $address = $mainBranch?->address ?? $this->country?->name;
        } else {
            // Fall back to query if branches not loaded
            $address = $this->mainBranch()?->address ?? $this->country?->name;
        }

        return [
            'id' => $this->id,
            'section_id' => $this->section_id,
            'name' => $this->name,
            'title' => $this->title,
            'description' => $this->description,
            'logo' => $this->logo_url,
            'rating' => (float) ($this->reviews_avg_rating ?? $this->reviews->avg('rating') ?? 0),
            'reviews_count' => (int) ($this->reviews_count ?? $this->reviews->count()),
            'max_discount' => $maxDiscount,
            'address' => $address,
        ];
    }
}
