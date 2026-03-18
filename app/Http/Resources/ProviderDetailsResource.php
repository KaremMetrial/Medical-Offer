<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProviderDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $maxDiscount = 0;
        if (isset($this->offers_max_discount_percent)) {
            $maxDiscount = $this->offers_max_discount_percent;
        } elseif ($this->relationLoaded('offers')) {
            $maxDiscount = $this->offers->max('discount_percent') ?? 0;
        } else {
            $maxDiscount = $this->offers()->max('discount_percent') ?? 0;
        }

        $mainBranch = $this->relationLoaded('branches') 
            ? $this->branches->firstWhere('is_main', true) 
            : $this->mainBranch();

        return [
            'id' => $this->id,
            'name' => $this->name,
            'title' => $this->title,
            'section' => $this->section?->type,
            'description' => $this->description,
            'logo' => $this->logo_url,
            'cover' => $this->cover_url,
            'rating' => (float) ($this->reviews_avg_rating ?? 0),
            'reviews_count' => (int) ($this->reviews_count ?? 0),
            'max_discount' => $maxDiscount,
            'experince_years' => (int) $this->experince_years,
            'visits_count' => (int) $this->views,
            'address' => $mainBranch?->address,
            'governorate' => $mainBranch?->governorate?->name,
            'city' => $mainBranch?->city?->name,
            'phone' => $mainBranch?->phone ?? $this->phone,
            'location' => [
                'lat' => $mainBranch?->lat,
                'lng' => $mainBranch?->lng,
            ],
            'branches' => $this->whenLoaded('branches', function() {
                return $this->branches->map(fn($branch) => [
                    'id' => $branch->id,
                    'name' => $branch->name,
                    'address' => $branch->address,
                    'phone' => $branch->phone,
                    'lat' => $branch->lat,
                    'lng' => $branch->lng,
                    'is_main' => (bool) $branch->is_main,
                    'governorate' => $branch->governorate?->name,
                    'city' => $branch->city?->name,
                ]);
            }),
        ];
    }
}
