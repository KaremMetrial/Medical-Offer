<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfferImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'offer_id',
        'path',
        'type',
        'sort_order'
    ];

    protected $casts = [
        'offer_id' => 'integer',
        'sort_order' => 'integer'
    ];

    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }

    // Check if image is main
    public function isMain()
    {
        return $this->sort_order === 0;
    }
}
