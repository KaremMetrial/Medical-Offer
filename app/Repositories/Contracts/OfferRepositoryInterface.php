<?php

namespace App\Repositories\Contracts;

interface OfferRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get Care and Beauty offers shown in home page.
     *
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getCareOffers(int $limit = 10);

    public function getOffersByProviderId($id, $columns = ['*'], $relations = []);

    /**
     * @param array $ids
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findInIds(array $ids);

    /**
     * @param int $providerId
     * @param int $perPage
     * @param string|null $search
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getPaginatedForProvider(int $providerId, int $perPage = 15, ?string $search = null);

    /**
     * @param int $providerId
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTopOffers(int $providerId, int $limit = 3);
}
