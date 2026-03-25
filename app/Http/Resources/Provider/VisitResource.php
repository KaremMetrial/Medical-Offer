<?php

namespace App\Http\Resources\Provider;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VisitResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $patient = $this->companion ?? $this->user;
        
        return [
            'id' => $this->id,
            'patient_name' => $patient?->name,
            'services' => $this->services ?: [],
            'visit_date' => $this->visit_date?->translatedFormat('d M Y, h:i A'),
            'location' => ($patient?->governorate?->name ? $patient->governorate->name . '، ' : '') . ($patient?->city?->name ?? ''),
            'status' => $this->status,
        ];
    }
}
