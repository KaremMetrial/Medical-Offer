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

    /**
     * Find a user by QR code or member ID.
     *
     * @param string $cardCode
     * @return \App\Models\User|null
     */
    public function findByCardCode(string $cardCode);

    /**
     * @param int $id
     * @return \App\Models\User|null
     */
    public function getDetails(int $id);

    /**
     * @param string $cardCode
     * @return \App\Models\User|null
     */
    public function getDetailsByCardCode(string $cardCode);
}
