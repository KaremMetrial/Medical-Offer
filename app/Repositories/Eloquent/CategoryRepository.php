<?php

namespace App\Repositories\Eloquent;

use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;

class CategoryRepository extends BaseRepository implements CategoryRepositoryInterface
{
    public function __construct(Category $model)
    {
        parent::__construct($model);
    }

    public function getActiveCategories($filter = null)
    {
        return $this->model->with('translations')
            ->filter($filter)
            ->where('is_active', true)
            ->where('is_show', true)
            ->orderBy('sort_order')
            ->get();
    }
    public function getParentActiveCategoriesBySectionId($sectionId, $filter = null)
    {
        return $this->model->with(['translations','children.translations'])
            ->filter($filter)
            ->where('is_active', true)
            ->where('is_show', true)
            ->where('section_id', $sectionId)
            ->where('parent_id', null)
            ->orderBy('sort_order')
            ->get();
    }
}
