<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManualNotification extends Model
{
    protected $fillable = [
        'title',
        'message',
        'user_id',
        'target_type',
    ];

    protected $casts = [
        'target_type' => \App\Enums\ManualNotificationTarget::class,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
