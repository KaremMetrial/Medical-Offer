<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CardRequest extends Model
{
    protected $fillable = [
        'user_id',
        'governorate_id',
        'city_id',
        'receiver_name',
        'receiver_phone',
        'address',
        'issuance_fee',
        'delivery_fee',
        'total_amount',
        'status',
    ];

    protected $casts = [
        'status' => \App\Enums\CardRequestStatus::class,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function governorate()
    {
        return $this->belongsTo(Governorate::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }
}
