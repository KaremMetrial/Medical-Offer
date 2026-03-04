<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfferPlanDiscount extends Model
{
    use HasFactory;

    protected $fillable = [
        'offer_id',
        'plan_id',
        'discount_percent'
    ];

    protected $casts = [
        'offer_id' => 'integer',
        'plan_id' => 'integer',
        'discount_percent' => 'integer'
    ];

    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }

    public function plan()
    {
        return $this->belongsTo(MemberPlan::class);
    }
}
