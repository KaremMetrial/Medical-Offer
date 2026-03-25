<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserCardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $subscription = $this->currentSubscription();
        
        return [
            'id' => $this->id,
            'user_name' => $this->name,
            'member_id' => $this->member_id,
            'expiry_date' => $subscription ? $subscription->end_at->format('Y/m/d') : now()->addYear()->format('Y/m/d'),
            'qr_code' => $this->qr_code,
            'card_title' => $subscription?->plan?->translation()?->name ?? 'Default Plan',
            'card_color' => $this->getCardColor($subscription?->plan?->id),
            'governorate' => $this->governorate?->name,
            'city' => $this->city?->name,
            'status' => $this->is_active ? 'active' : 'inactive',
            'subscription_status' => $subscription ? 'active' : 'inactive',
        ];
    }

    protected function getCardColor($planId)
    {
        $colors = [
            1 => '#008AB8', // Blue
            2 => '#94772C', // Gold
            3 => '#CC5490', // Pink
        ];

        return $colors[$planId] ?? '#212529';
    }
}
