<?php

namespace App\Repositories\Contracts;

interface UserRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Find a user by phone number.
     *
     * @param string $phone
     * @return \App\Models\User|null
     */
    public function findByPhone(string $phone);
}
