<?php

namespace App\Filters;

class CardRequestFilter extends AbstractFilter
{
    public function status($value)
    {
        if (!$value) return $this->builder;
        return $this->builder->where('status', $value);
    }

    public function governorate_id($value)
    {
        if (!$value) return $this->builder;
        return $this->builder->where('governorate_id', $value);
    }

    public function city_id($value)
    {
        if (!$value) return $this->builder;
        return $this->builder->where('city_id', $value);
    }
}
