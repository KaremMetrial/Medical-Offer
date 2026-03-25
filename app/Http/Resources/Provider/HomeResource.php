<?php

namespace App\Http\Resources\Provider;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HomeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'appbar' => $this['appbar'] ?? null,
            'stats' => [
                [
                    'label' => __('message.visits_count'),
                    'value' => (string)$this['stats']['visits_count'],
                    'icon' => asset('storage/hospital.png'),
                ],
                [
                    'label' => __('message.clinic_collection'),
                    'value' => number_format($this['stats']['total_collection']) . ' ' . __('message.currency'),
                    'icon' => asset('storage/currency.png'),
                ],
                [
                    'label' => __('message.rating'),
                    'value' => (string)$this['stats']['rating'],
                    'icon' => asset('storage/Vector.png'),
                ],
                [
                    'label' => __('message.experience_years'),
                    'value' => $this['stats']['experience_years'] . ' ' . __('message.years'),
                    'icon' => asset('storage/aid_9886470.png'),
                ],
            ],
            'top_services' => $this['top_services'],
            'visit_movement' => collect($this['visit_movement'])->map(function($visit) {
                return [
                    'label' => $visit['label'],
                    'count' => $visit['count'],
                ];
            }),
            'recent_reviews' => $this['recent_reviews']->map(function($review) {
                return [
                    'user_name' => $review->user->name ?? 'Guest',
                    'rating' => $review->rating,
                    'comment' => $review->comment,
                    'date' => $review->created_at->format('d M Y'),
                    'services' => $review->offer->name,
                ];
            }),
        ];
    }
}
