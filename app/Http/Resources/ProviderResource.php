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
        // Get max discount using already loaded offers relationship or withMax attribute
        $maxDiscount = 0;
        if (isset($this->offers_max_discount_percent)) {
            $maxDiscount = $this->offers_max_discount_percent;
        } elseif ($this->relationLoaded('offers')) {
            $maxDiscount = $this->offers->max('discount_percent') ?? 0;
        } else {
            // Fall back to query if nothing is available (try to avoid this in controllers)
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
            'rating' => (float) ($this->reviews_avg_rating ?? 0),
            'reviews_count' => (int) ($this->reviews_count ?? 0),
            'max_discount' => $maxDiscount,
            'address' => $address,
        ];
    }
}
