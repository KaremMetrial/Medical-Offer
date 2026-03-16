<?php

namespace App\Filters;

use App\Enums\RatingType;

class ProviderFilter extends AbstractFilter
{
    public function section($value)
    {
        if (!$value) return $this->builder;
        return $this->builder->whereHas('section', fn($q) => $q->where('type', $value));
    }

    public function rating($value)
    {
        if (!$value) return $this->builder;
        
        $this->builder->withAvg('reviews', 'rating');

        return match ($value) {
            'five' => $this->builder->having('reviews_avg_rating', '=', 5),
            'four_and_above' => $this->builder->having('reviews_avg_rating', '>=', 4),
            'three_and_above' => $this->builder->having('reviews_avg_rating', '>=', 3),
            'two_and_above' => $this->builder->having('reviews_avg_rating', '>=', 2),
            default => $this->builder
        };
    }

    public function discount($value)
    {
        if (!$value) return $this->builder;

        return $this->builder->whereHas('offers', function ($q) use ($value) {
            match ($value) {
                'ten_and_twenty' => $q->whereBetween('discount_percent', [10, 20]),
                'twenty_and_forty' => $q->whereBetween('discount_percent', [20, 40]),
                'forty_and_sixty' => $q->whereBetween('discount_percent', [40, 60]),
                'sixty_and_eighty' => $q->whereBetween('discount_percent', [60, 80]),
                'eighty_and_one_hundred' => $q->whereBetween('discount_percent', [80, 100]),
                default => null
            };
        });
    }

    public function governorate_id($value)
    {
        if (!$value) return $this->builder;
        return $this->builder->whereHas('branches', fn($q) => $q->where('governorate_id', $value));
    }
}
