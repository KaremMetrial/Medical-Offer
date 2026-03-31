<?php

namespace App\Http\Resources\Filters;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GovernorateFilterResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'value' => (string) $this->id,
            'label' => $this->name,
        ];
    }
}
