<?php

namespace App\Repositories\Contracts;

interface SectionRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get active sections with their translations.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    /**
     * Get sections with their home data (categories, providers, offers).
     */
    public function getHomeDataSections();
}
