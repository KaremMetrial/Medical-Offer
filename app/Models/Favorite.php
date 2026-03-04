<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'offer_id',
        'provider_id'
    ];

    protected $casts = [
        'user_id' => 'integer',
        'offer_id' => 'integer',
        'provider_id' => 'integer'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }

    // Check if favorite is for offer
    public function isOfferFavorite()
    {
        return $this->offer_id !== null;
    }

    // Check if favorite is for provider
    public function isProviderFavorite()
    {
        return $this->provider_id !== null;
    }
}
