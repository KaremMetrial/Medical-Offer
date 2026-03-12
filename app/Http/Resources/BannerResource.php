<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BannerResource extends JsonResource
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
            'image' => $this->src,
            'link_type' => $this->link_type,
            'link_id' => $this->link_id,
            'external_url' => $this->external_url,
            'title' => $this->title,
        ];
    }
}
