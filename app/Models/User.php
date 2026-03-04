<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'password',
        'otp_hash',
        'otp_expired_at',
        'avatar',
        'role',
        'parent_user_id',
        'is_active'
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'otp_hash',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'otp_expired_at' => 'datetime',
        'password' => 'hashed',
        'role' => 'string',
        'is_active' => 'boolean'
    ];

    public function parentUser()
    {
        return $this->belongsTo(User::class, 'parent_user_id');
    }

    public function children()
    {
        return $this->hasMany(User::class, 'parent_user_id');
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function offerViews()
    {
        return $this->hasMany(OfferView::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function payments()
    {
        return $this->morphMany(Payment::class, 'payable');
    }

    public function providers()
    {
        return $this->belongsToMany(Provider::class, 'user_provider')
                    ->withPivot('branch_id')
                    ->withTimestamps();
    }

    public function branches()
    {
        return $this->belongsToMany(ProviderBranch::class, 'user_provider')
                    ->withPivot('provider_id')
                    ->withTimestamps();
    }

    // Check if user is provider role
    public function isProvider()
    {
        return $this->role === 'provider';
    }

    // Check if user can manage a specific provider
    public function canManageProvider($providerId)
    {
        return $this->providers()->where('provider_id', $providerId)->exists();
    }

    // Check if user can manage a specific branch
    public function canManageBranch($branchId)
    {
        return $this->branches()->where('branch_id', $branchId)->exists();
    }

    // Get user's main provider (first one if multiple)
    public function mainProvider()
    {
        return $this->providers()->first();
    }

    // Check if user has any provider associations
    public function hasProviderAccess()
    {
        return $this->providers()->exists();
    }

    // Check if user is admin
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    // Check if user is active
    public function isActive()
    {
        return $this->is_active;
    }

    // Get current subscription
    public function currentSubscription()
    {
        return $this->subscriptions()
            ->where('status', 'active')
            ->where('payment_status', 'paid')
            ->where('start_at', '<=', now())
            ->where('end_at', '>=', now())
            ->first();
    }

    // Check if user has active subscription
    public function hasActiveSubscription()
    {
        return $this->currentSubscription() !== null;
    }

    // Get user's plan
    public function plan()
    {
        $subscription = $this->currentSubscription();
        return $subscription ? $subscription->plan : null;
    }

    // Check if OTP is valid
    public function isOtpValid($otp)
    {
        return $this->otp_hash &&
               $this->otp_expired_at &&
               $this->otp_expired_at > now() &&
               password_verify($otp, $this->otp_hash);
    }
}
