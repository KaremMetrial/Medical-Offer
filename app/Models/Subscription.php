<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'plan_id',
        'start_at',
        'end_at',
        'status',
        'payment_status'
    ];

    protected $casts = [
        'user_id' => 'integer',
        'plan_id' => 'integer',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'status' => 'string',
        'payment_status' => 'string'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plan()
    {
        return $this->belongsTo(MemberPlan::class);
    }

    public function payments()
    {
        return $this->morphMany(Payment::class, 'payable');
    }

    // Check if subscription is active
    public function isActive()
    {
        return $this->status === 'active' &&
               $this->payment_status === 'paid' &&
               $this->start_at <= now() &&
               $this->end_at >= now();
    }

    // Check if subscription is expired
    public function isExpired()
    {
        return $this->end_at < now();
    }

    // Get remaining days
    public function remainingDays()
    {
        if ($this->isExpired()) {
            return 0;
        }
        return $this->end_at->diffInDays(now());
    }
}
