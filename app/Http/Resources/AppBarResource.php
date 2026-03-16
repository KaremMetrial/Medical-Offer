<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppBarResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // When user is null, $this->resource is the default data array from HomeController
        if (is_array($this->resource)) {
            return $this->resource;
        }

        $defaultCountry = app(\App\Repositories\Contracts\CountryRepositoryInterface::class)->getDefaultCountry();

        // When user is provided, $this->resource is a User model
        return [
            'title' => __('home.welcome_back', ['name' => $this->name]),
            'subtitle' => __('home.good_day'),
            'avatar' => $this->avatar_url,
            'unread_notifications' => $this->unreadNotificationsCount(),
            'country_flag' => $this->country?->src ?? $defaultCountry?->src,
            'search_placeholder' => __('home.search_placeholder'),
        ];
    }
}
