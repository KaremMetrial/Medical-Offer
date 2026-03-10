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

            // Optional: Include relations if loaded
            'country'        => new JsonResource($this->whenLoaded('country')),
            'governorate'    => new JsonResource($this->whenLoaded('governorate')),
            'city'           => new JsonResource($this->whenLoaded('city')),

            'created_at'     => $this->created_at,
            'updated_at'     => $this->updated_at,
        ];
    }
}
