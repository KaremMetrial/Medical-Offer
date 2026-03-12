<?php

namespace App\Services;


use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

use App\Repositories\Contracts\UserRepositoryInterface;

class AuthService
{
    protected $otpService;
    protected $userRepository;

    public function __construct(OtpService $otpService, UserRepositoryInterface $userRepository)
    {
        $this->otpService = $otpService;
        $this->userRepository = $userRepository;
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
        $user = $this->userRepository->findByPhone($phone);

        if (!$user) {
            $user = $this->userRepository->create([
                'phone' => $phone,
                'name' => 'Guest',
                'is_active' => false
            ]);
        }

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
        $user = $this->userRepository->findByPhone($phone);

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
        $user = $this->userRepository->findByPhone($data['phone']);
        if (!$user) {
            throw new \Exception(__('message.user_not_found'));
        }
        if (isset($data['password']) && $data['password'] != null) {
            $user->update([
                'password'  => Hash::make($data['password']),
            ]);
        }

        // Update user data, mark as active
        $user->update([
            'name'      => $data['name'],
            'email'     => $data['email'] ?? null,
            'country_id' => $data['country_id'] ?? null,
            'city_id'    => $data['city_id'] ?? null,
            'avatar'     => $data['avatar'] ?? null,
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
