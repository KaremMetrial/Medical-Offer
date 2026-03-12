<?php

namespace App\Repositories\Contracts;

interface CategoryRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get active categories shown in home page.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveCategories();

    /**
     * Get parent active categories by section id.
     *
     * @param int $sectionId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getParentActiveCategoriesBySectionId($sectionId);
}
