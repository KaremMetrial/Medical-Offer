<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StoryResource extends JsonResource
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
            'type' => $this->story_type,
            'media_url' => $this->src,
            'is_viewed' => (bool) $this->isViewed(),
            'external_link' => $this->external_link,
            'expiry_time' => $this->expiry_time,
            'provider' => new ProviderStoryResource($this->whenLoaded('provider')),
        ];
    }
}
