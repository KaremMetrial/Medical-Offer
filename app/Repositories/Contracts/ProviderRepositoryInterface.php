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

    /**
     * Get the next provider ID that has active stories.
     *
     * @param int $currentProviderId
     * @return int|null
     */
    public function getNextProviderIdWithStories(int $currentProviderId): ?int;

    /**
     * Get filtered and paginated providers.
     *
     * @param mixed $filters
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getFilteredPaginatedProviders($filters = [], int $perPage = 15);

    /**
     * Get providers by category.
     *
     * @param int $categoryId
     * @param mixed $filters
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getProvidersByCategory(int $categoryId, $filters = [], int $perPage = 15);
    
    public function getDetails($id);

    /**
     * @param int $id
     * @return \App\Models\Provider|null
     */
    public function getStats(int $id);
}
