<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
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
            'rating' => (float) $this->rating,
            'comment' => $this->comment,
            'service_name' => $this->offer?->name ?? __('message.consultation'),
            'date' => $this->created_at->format('d M y'),
            'user_name' => $this->user?->name,
            'user_avatar' => $this->user?->avatar_url,
        ];
    }
}
