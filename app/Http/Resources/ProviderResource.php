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
        return [
            'id' => $this->id,
            'name' => $this->name,
            'title' => $this->title,
            'description' => $this->description,
            'logo' => $this->logo_url,
            'rating' => round($this->reviews()->avg('rating') ?? 0, 1),
            'reviews_count' => $this->reviews()->count(),
            'max_discount' => $this->offers()->max('discount_percent') ?? 0,
            'address' => $this->mainBranch()?->address ?? $this->country?->name,
        ];
    }
}
