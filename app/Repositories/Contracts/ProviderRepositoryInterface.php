<?php

namespace App\Repositories\Contracts;

interface ProviderRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get providers with active stories.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getWithActiveStories();

    /**
     * Get elite (verified/highly rated) doctors.
     *
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getEliteDoctors(int $limit = 10);

    /**
     * Get latest active medical centers.
     *
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getMedicalCenters(int $limit = 10);
}
