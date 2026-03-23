<?php

namespace App\Models;

use App\Enums\WithdrawalStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Withdrawal extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'amount',
        'fee',
        'net_amount',
        'status',
        'method',
        'payment_details',
        'rejection_reason',
        'reference_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'fee' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'status' => WithdrawalStatus::class,
        'payment_details' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', WithdrawalStatus::PENDING);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', WithdrawalStatus::APPROVED);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', WithdrawalStatus::REJECTED);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', WithdrawalStatus::COMPLETED);
    }
}
