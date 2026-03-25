<?php

namespace App\Http\Resources\Provider;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VisitDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $patient = $this->companion ?? $this->user;
        $user = auth()->user();
        $currency = $user->country?->currency_symbol ?? __('message.egp_symbol');
        
        return [
            'id' => $this->id,
            'card_info' => new \App\Http\Resources\UserCardResource($patient),
            'visit_info' => [
                'id' => $this->id,
                'date' => $this->visit_date?->translatedFormat('d M Y'),
                'time' => $this->visit_date?->translatedFormat('h:i A'),
                'location' => ($patient?->governorate?->name ? $patient->governorate->name . '، ' : '') . ($patient?->city?->name ?? ''),
                'status' => $this->status,
                'status_label' => __('message.status_' . $this->status),
            ],
            'treatment_plan' => $this->getTreatmentPlan(),
            'payment_summary' => [
                'total_amount' => (float)$this->paid_amount + (float)$this->discount_amount,
                'discount_amount' => (float)$this->discount_amount,
                'paid_amount' => (float)$this->paid_amount,
                'currency' => $currency,
            ],
            'labels' => [
                'patient_section' => __('message.patient'),
                'treatment_plan' => __('message.treatment_plan'),
                'payment_details' => __('message.payment_details'),
                'total_amount' => __('message.total_amount'),
                'discount_amount' => __('message.discount_amount'),
                'paid_amount' => __('message.paid_amount'),
            ]
        ];
    }

    /**
     * Resolve the treatment plan from the Offer model or the stored JSON.
     */
    protected function getTreatmentPlan(): array
    {
        $services = $this->services ?: [];
        $serviceNames = [];

        // Collect all names/titles to search for
        foreach ($services as $service) {
            if (is_array($service) && isset($service['title'])) {
                $serviceNames[] = $service['title'];
            } elseif (is_string($service)) {
                $serviceNames[] = $service;
            }
        }

        // Fetch corresponding offers for this provider to get current data
        $offers = \App\Models\Offer::where('provider_id', $this->provider_id)
            ->whereHas('translations', function($q) use ($serviceNames) {
                $q->whereIn('name', $serviceNames);
            })->get()->keyBy(function($offer) {
                return $offer->name;
            });

        return array_map(function($service) use ($offers) {
            $name = is_array($service) ? ($service['title'] ?? '') : $service;
            $storedDiscount = is_array($service) ? ($service['discount'] ?? null) : null;
            
            if (isset($offers[$name])) {
                return [
                    'title'       => $offers[$name]->name,
                    'description' => $offers[$name]->description,
                    'discount'    => $storedDiscount ?? ($offers[$name]->discount_percent . '%'),
                ];
            }

            return [
                'title'       => $name,
                'description' => is_array($service) ? ($service['description'] ?? null) : null,
                'discount'    => $storedDiscount,
            ];
        }, $services);
    }
}
