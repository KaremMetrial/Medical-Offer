<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    protected $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    /**
     * Request an OTP for a phone number.
     *
     * @param string $phone
     * @return array Response data including expires_in and testing OTP
     */
    public function requestOtp(string $phone): array
    {
        // Find or create a draft user record
        // This allows registration flow too
        $user = User::firstOrCreate(
            ['phone' => $phone],
            ['name' => 'Guest', 'is_active' => false]
        );

        $otp = $this->otpService->send($user);

        $response = [
            'message'    => __('message.otp_sent_successfully'),
            'expires_in' => 300, // 5 minutes in seconds
        ];

        // Include OTP in the response if we are in local or testing environments
        if (app()->environment(['local', 'testing'])) {
            $response['test_otp'] = $otp;
        }

        return $response;
    }

    /**
     * Verify OTP and return a session/token.
     *
     * @param string $phone
     * @param string $otp
     * @return array Result with token if registered, or status for registration
     */
    public function verifyOtp(string $phone, string $otp): array
    {
        $user = User::where('phone', $phone)->first();

        if (!$user || !$this->otpService->verify($user, $otp)) {
            throw ValidationException::withMessages([
                'otp' => [__('message.invalid_otp')],
            ]);
        }

        // If user is already active/registered, log them in
        if ($user->is_active) {
            $token = $user->createToken('auth_token')->plainTextToken;
            return [
                'registered' => true,
                'token'      => $token,
                'user'       => $user,
            ];
        }

        // Return status for registration completion
        return [
            'registered' => false,
            'message'    => __('message.please_complete_registration'),
            'phone'      => $phone,
            'user'       => null,
        ];
    }

    /**
     * Complete registration for a user who verified their OTP.
     *
     * @param array $data
     * @return array Login data
     */
    public function register(array $data): array
    {
        $user = User::where('phone', $data['phone'])->first();

        if (!$user) {
            throw new \Exception(__('message.user_not_found'));
        }

        // Update user data, mark as active
        $user->update([
            'name'      => $data['name'],
            'email'     => $data['email'] ?? null,
            'password'  => Hash::make($data['password']),
            'country_id' => $data['country_id'] ?? null,
            'city_id'    => $data['city_id'] ?? null,
            'is_active'  => true,
            'role'       => 'user', // Default role
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'token' => $token,
            'user'  => $user,
        ];
    }
}
