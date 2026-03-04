<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'payable_type',
        'payable_id',
        'provider_id',
        'amount',
        'method',
        'provider_ref',
        'status'
    ];

    protected $casts = [
        'payable_id' => 'integer',
        'provider_id' => 'integer',
        'amount' => 'decimal:2',
        'status' => 'string'
    ];

    public function payable()
    {
        return $this->morphTo();
    }

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }

    // Check if payment is successful
    public function isSuccessful()
    {
        return $this->status === 'paid';
    }

    // Check if payment is pending
    public function isPending()
    {
        return $this->status === 'pending';
    }

    // Check if payment failed
    public function isFailed()
    {
        return $this->status === 'failed';
    }

    // Check if payment was refunded
    public function isRefunded()
    {
        return $this->status === 'refunded';
    }
}
