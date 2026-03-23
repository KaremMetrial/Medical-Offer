<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    protected $fillable = [
        'user_id',
        'companion_id',
        'provider_id',
        'visit_date',
        'services',
        'paid_amount',
        'discount_amount',
        'comment',
        'status',
    ];

    protected $casts = [
        'visit_date' => 'datetime',
        'services' => 'array',
        'paid_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function companion()
    {
        return $this->belongsTo(User::class, 'companion_id');
    }

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }
}
