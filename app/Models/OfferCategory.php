<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfferCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'offer_id',
        'category_id'
    ];

    protected $casts = [
        'offer_id' => 'integer',
        'category_id' => 'integer'
    ];

    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
