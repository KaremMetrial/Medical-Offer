<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'name'           => $this->name,
            'email'          => $this->email,
            'phone'          => $this->phone,
            'avatar'         => $this->avatar_url,
            'role'           => $this->role,
            'is_active'      => $this->is_active,
            'country_id'     => $this->country_id,
            'governorate_id' => $this->governorate_id,
            'city_id'        => $this->city_id,
            'gender'         => $this->gender,
            'nationality_id' => $this->nationality_id,

            // Optional: Include relations if loaded
            'country'        => JsonResource::make($this->whenLoaded('country')),
            'governorate'    => JsonResource::make($this->whenLoaded('governorate')),
            'city'           => JsonResource::make($this->whenLoaded('city')),
            'nationality'    => JsonResource::make($this->whenLoaded('nationality')),

            'created_at'     => $this->created_at?->format('Y-m-d'),
            'updated_at'     => $this->updated_at?->format('Y-m-d'),
        ];
    }
}
