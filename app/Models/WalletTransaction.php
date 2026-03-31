<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type', // credit, debit, deposit, withdraw
        'status', // pending, success, failed
        'amount',
        'balance_after',
        'description',
        'reference',
        'provider_ref',
        'metadata',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'type' => \App\Enums\WalletTransactionType::class,
        'status' => \App\Enums\WalletTransactionStatus::class,
        'metadata' => 'array',
    ];



    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeCredits($query)
    {
        return $query->where('type', 'credit');
    }

    public function scopeDebits($query)
    {
        return $query->where('type', 'debit');
    }
}
