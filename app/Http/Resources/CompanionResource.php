<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'avatar' => $this->getAvatarUrlAttribute(),
            'relationship' => $this->relationship?->getLabel(),
            'relationship_key' => $this->relationship?->value,
            'status' => $this->is_active ? __('message.active') : __('message.inactive'),
            'status_class' => $this->is_active ? 'active' : 'inactive',
            'attachments' => $this->attachments->map(fn($a) => [
                'id' => $a->id,
                'path' => $a->url,
                'type' => $a->type
            ])
        ];
    }
}
