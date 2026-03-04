<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfferTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'offer_id',
        'local',
        'name',
        'description',
        'terms'
    ];

    protected $casts = [
        'offer_id' => 'integer'
    ];

    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }
}
