<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProviderStoryResource extends JsonResource
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
            'logo' => $this->logo_url ?? asset('storage/default/provider.png'),
            'display_duration' => config('settings.story_display_duration', 30),
            'stories' => HomeStoryResource::collection($this->whenLoaded('stories')),
            'next_provider_id' => app(\App\Repositories\Contracts\ProviderRepositoryInterface::class)->getNextProviderIdWithStories($this->id),
        ];
    }
}
