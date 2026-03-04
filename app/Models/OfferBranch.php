<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfferBranch extends Model
{
    use HasFactory;

    protected $fillable = [
        'offer_id',
        'branch_id'
    ];

    protected $casts = [
        'offer_id' => 'integer',
        'branch_id' => 'integer'
    ];

    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }

    public function branch()
    {
        return $this->belongsTo(ProviderBranch::class);
    }
}
