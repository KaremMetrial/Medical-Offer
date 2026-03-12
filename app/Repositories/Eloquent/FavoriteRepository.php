<?php

namespace App\Repositories\Eloquent;

use App\Models\Favorite;
use App\Repositories\Contracts\FavoriteRepositoryInterface;

class FavoriteRepository extends BaseRepository implements FavoriteRepositoryInterface
{
    public function __construct(Favorite $model)
    {
        parent::__construct($model);
    }

    public function toggleFavorite(int $userId, array $data)
    {
        $query = $this->model->where('user_id', $userId);

        if (isset($data['offer_id'])) {
            $query->where('offer_id', $data['offer_id']);
        } else {
            $query->where('provider_id', $data['provider_id']);
        }

        $favorite = $query->first();

        if ($favorite) {
            $favorite->delete();
            return ['status' => 'removed'];
        }

        $this->model->create(array_merge($data, ['user_id' => $userId]));
        return ['status' => 'added'];
    }

    public function getUserFavorites(int $userId)
    {
        return $this->model->where('user_id', $userId)
            ->with(['offer.translations', 'provider.translations'])
            ->get();
    }
}
