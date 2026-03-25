<?php

namespace App\Repositories\Contracts;

interface VisitRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get the count of visits for a specific provider.
     *
     * @param int $providerId
     * @return int
     */
    public function countByProviderId(int $providerId): int;

    /**
     * Get the total amount collected from visits for a specific provider.
     *
     * @param int $providerId
     * @return float
     */
    public function sumPaidAmountByProviderId(int $providerId): float;

    /**
     * Get visit movement counts (daily or monthly).
     *
     * @param int $providerId
     * @param string $filter 'day' or 'month'
     * @return array
     */
    public function getVisitMovement(int $providerId, string $filter): array;

    /**
     * Get paginated visits for a provider.
     * 
     * @param int $providerId
     * @param int $perPage
     * @param string|null $search
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getPaginatedForProvider(int $providerId, int $perPage = 10, ?string $search = null);

    /**
     * Store a new visit.
     *
     * @param array $data
     * @return \App\Models\Visit
     */
    public function store(array $data);

    /**
     * Get visit details with loaded relationships.
     *
     * @param int $id
     * @param int $providerId
     * @return \App\Models\Visit
     */
    public function getDetails(int $id, int $providerId);
}
