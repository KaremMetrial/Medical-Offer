<?php

namespace App\Repositories\Contracts;

interface BannerRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get active banners for home slider.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveBanners();
}
