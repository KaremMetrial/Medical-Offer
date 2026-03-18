<?php

namespace App\Repositories\Contracts;

interface CategoryRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get active categories shown in home page.
     *
     * @param mixed $filter
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveCategories($filter = null);

    /**
     * Get parent active categories by section id.
     *
     * @param int $sectionId
     * @param mixed $filter
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getParentActiveCategoriesBySectionId($sectionId, $filter = null);
}
