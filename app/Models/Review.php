<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'provider_id',
        'offer_id',
        'rating',
        'comment',
        'status'
    ];

    protected $casts = [
        'user_id' => 'integer',
        'provider_id' => 'integer',
        'offer_id' => 'integer',
        'rating' => 'integer',
        'status' => 'string'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }

    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }

    // Check if review is for provider
    public function isProviderReview()
    {
        return $this->provider_id !== null;
    }

    // Check if review is for offer
    public function isOfferReview()
    {
        return $this->offer_id !== null;
    }

    // Check if review is approved
    public function isApproved()
    {
        return $this->status === 'approved';
    }
}
