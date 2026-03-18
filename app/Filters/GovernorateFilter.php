<?php

namespace App\Filters;

class GovernorateFilter extends AbstractFilter
{
    /**
     * Filter by search term in governorates translations.
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
     * Filter by country ID.
     *
     * @param int $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function country_id($value)
    {
        if (!$value) return $this->builder;

        return $this->builder->where('country_id', $value);
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
