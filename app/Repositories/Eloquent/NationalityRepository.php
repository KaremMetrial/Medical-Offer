<?php

namespace App\Repositories\Eloquent;

use App\Models\Nationality;
use App\Repositories\Contracts\NationalityRepositoryInterface;

class NationalityRepository extends BaseRepository implements NationalityRepositoryInterface
{
    public function __construct(Nationality $model)
    {
        parent::__construct($model);
    }
}
