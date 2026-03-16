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
}
