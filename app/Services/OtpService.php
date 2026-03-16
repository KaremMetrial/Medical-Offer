<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\SendOtpNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class OtpService
{
    /**
     * Generate and send OTP to a user.
     *
     * @param User $user
     * @return string
     */
    public function send(User $user): string
    {
        // Generate 6-digit numeric OTP
        $otp = env('APP_ENV') === 'local' ? '1111' : (string) random_int(1000, 9999);

        // Save hashed OTP and expiry to user record
        // OTP is valid for 5 minutes
        $user->update([
            'otp_hash' => Hash::make($otp),
            'otp_expired_at' => now()->addMinutes(5),
        ]);

        // Send Notification (Email, SMS, WhatsApp)
        $user->notify(new SendOtpNotification($otp));

        return $otp;
    }

    /**
     * Verify the provided OTP for a user.
     *
     * @param User $user
     * @param string $otp
     * @return bool
     */
    public function verify(User $user, string $otp): bool
    {
        // Check if OTP exists and is not expired
        if (!$user->otp_hash || !$user->otp_expired_at || $user->otp_expired_at->isPast()) {
            return false;
        }

        // Verify hash
        if (Hash::check($otp, $user->otp_hash)) {
            // Clear OTP after successful verification to prevent reuse
            $user->update([
                'otp_hash' => null,
                'otp_expired_at' => null,
            ]);

            return true;
        }

        return false;
    }
}
