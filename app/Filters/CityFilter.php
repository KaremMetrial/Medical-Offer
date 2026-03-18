<?php

namespace App\Filters;

class CityFilter extends AbstractFilter
{
    /**
     * Filter by search term in cities translations.
     *
     * @param string $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function search($value)
    {
        if (!$value) return $this->builder;

        return $this->builder->whereHas('translations', function ($q) use ($value) {
            $q->where('name', 'like', '%' . $value . '%');
        });
    }

    /**
     * Filter by governorate ID.
     *
     * @param int $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function governorate_id($value)
    {
        if (!$value) return $this->builder;

        return $this->builder->where('governorate_id', $value);
    }

    /**
     * Filter by active status.
     *
     * @param mixed $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function is_active($value)
    {
        if ($value === null) return $this->builder;

        return $this->builder->where('is_active', filter_var($value, FILTER_VALIDATE_BOOLEAN));
    }
}
