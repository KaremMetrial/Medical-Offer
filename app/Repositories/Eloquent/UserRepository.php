<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    /**
     * UserRepository constructor.
     *
     * @param User $model
     */
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    /**
     * @inheritdoc
     */
    public function findByPhone(string $phone)
    {
        return $this->model->where('phone', $phone)->first();
    }

    /**
     * @inheritdoc
     */
    public function findByCardCode(string $cardCode)
    {
        return $this->model->where('qr_code', $cardCode)
            ->orWhere('member_id', $cardCode)
            ->first();
    }

    /**
     * @inheritdoc
     */
    public function getDetails(int $id)
    {
        return $this->model->with([
            'country',
            'governorate.translations',
            'city.translations',
            'subscriptions.plan.translations'
        ])
        ->find($id);
    }

    /**
     * @inheritdoc
     */
    public function getDetailsByCardCode(string $cardCode)
    {
        return $this->model->with([
            'country',
            'governorate.translations',
            'city.translations',
            'subscriptions.plan.translations'
        ])
        ->where('qr_code', $cardCode)
        ->orWhere('member_id', $cardCode)
        ->first();
    }
}
