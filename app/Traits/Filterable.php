<?php

namespace App\Traits;

use App\Filters\AbstractFilter;
use Illuminate\Database\Eloquent\Builder;

trait Filterable
{
    public function scopeFilter(Builder $builder, $filter)
    {
        if ($filter instanceof AbstractFilter) {
            return $filter->apply($builder);
        }
        
        return $builder;
    }
}
