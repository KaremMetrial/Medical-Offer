<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Support\Facades\Storage;
use App\Enums\RelationshipType;

class User extends Authenticatable implements MustVerifyEmail, FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable;
    
    protected $current_subscription_memo = null;
    protected $is_subscription_memoized = false;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'password',
        'otp_hash',
        'otp_expired_at',
        'avatar',
        'role',
        'country_id',
        'governorate_id',
        'city_id',
        'parent_user_id',
        'nationality_id',
        'gender',
        'balance',
        'relationship',
        'is_active',
        'companion_status',
        'member_id',
        'qr_code',
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
        'country_id' => 'integer',
        'governorate_id' => 'integer',
        'city_id' => 'integer',
        'balance' => 'decimal:2',
        'is_active' => 'boolean',
        'relationship' => RelationshipType::class,
        'gender' => 'string',
        'companion_status' => 'string'
    ];
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->isAdmin();
    }
    public function nationality()
    {
        return $this->belongsTo(Nationality::class);
    }
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function governorate()
    {
        return $this->belongsTo(Governorate::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }
    public function cardRequests()
    {
        return $this->hasMany(CardRequest::class);
    }
    public function walletTransactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }
    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class);
    }

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

    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
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
        return $this->role === 'admin' || $this->role === 'super_admin';
    }

    // Check if user is active
    public function isActive()
    {
        return $this->is_active;
    }

    // Get current subscription
    public function currentSubscription()
    {
        if ($this->is_subscription_memoized) {
            return $this->current_subscription_memo;
        }

        $this->current_subscription_memo = $this->subscriptions()
            ->with('plan.translations')
            ->where('status', 'active')
            ->where('payment_status', 'paid')
            ->where('start_at', '<=', now())
            ->where('end_at', '>=', now())
            ->first();
        
        $this->is_subscription_memoized = true;

        return $this->current_subscription_memo;
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

    public function getAvatarUrlAttribute(): ?string
    {
        return $this->avatar ? \Illuminate\Support\Facades\Storage::disk('public')->url($this->avatar) : asset('storage/users/avatars/avatar.jpg');
    }

    public function getImagePathAttribute(): ?string
    {
        return $this->avatar;
    }

    public function getSrcAttribute(): ?string
    {
        return $this->getAvatarUrlAttribute();
    }
    public function unreadNotificationsCount(): int
    {
        return $this->unread_notifications_count ?? $this->notifications()->where('read_at', null)->count() ?? 0;
    }

    /**
     * Route notifications for the Firebase channel.
     *
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return string|array|null
     */
    public function routeNotificationForFirebase($notification)
    {
        return $this->tokens()
            ->whereNotNull('fcm_token')
            ->pluck('fcm_token')
            ->toArray();
    }
}
