<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HomeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'appbar' => $this['appbar'] ?? null,
            'stories' => $this['stories'] ?? [],
            'banners' => $this['banners'] ?? [],
            'sections' => $this['sections'] ?? [],
            'membership_banner' => $this['membership_banner'] ?? null,
            'featured' => $this['featured'] ?? [],
        ];
    }
}
