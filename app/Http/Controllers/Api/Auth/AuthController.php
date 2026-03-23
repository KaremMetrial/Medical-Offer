<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\SendOtpRequest;
use App\Http\Requests\Auth\UpdateProfileRequest;
use App\Http\Requests\Auth\VerifyOtpRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;

class AuthController extends BaseController
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Send OTP to a phone number.
     * Rate limiting applied at route level.
     */
    public function sendOtp(SendOtpRequest $request): JsonResponse
    {
        $data = $this->authService->requestOtp($request->phone);

        $payload = [
            'expires_in' => $data['expires_in'],
        ];

        if (array_key_exists('test_otp', $data)) {
            $payload['test_otp'] = $data['test_otp'];
        }

        return $this->successResponse($payload, $data['message']);
    }

    /**
     * Verify OTP for a phone number.
     */
    public function verifyOtp(VerifyOtpRequest $request): JsonResponse
    {
        $result = $this->authService->verifyOtp($request->phone, $request->otp, $request->fcm_token);

        if ($result['registered'] && isset($result['user'])) {
            $result['user'] = new UserResource($result['user']);
        }

        return $this->successResponse($result, $result['message'] ?? __('message.login_successfully'));
    }

    /**
     * Complete registration.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $data = $request->validated();

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $this->storeImage($request->file('avatar'), 'users/avatars');
        }

        $result = $this->authService->register($data);

        if (isset($result['user'])) {
            $result['user'] = new UserResource($result['user']);
        }

        return $this->successResponse($result, __('message.registration_successfully'));
    }

    public function profile(Request $request): JsonResponse
    {
        $user = $request->user();
        return $this->successResponse([
            'user' => new UserResource($user),
        ], __('message.profile_retrieved_successfully'));
    }

    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        $user = $request->user();
        $data = $request->validated();
        if ($request->hasFile('avatar')) {
            $data['avatar'] = $this->storeImage($request->file('avatar'), 'users/avatars');
        }
        $user->update($data);

        if ($request->fcm_token) {
            $user->currentAccessToken()->update(['fcm_token' => $request->fcm_token]);
        }
        return $this->successResponse([
            'user' => new UserResource($user),
        ], __('message.profile_updated_successfully'));
    }

    public function updateFcmToken(Request $request): JsonResponse
    {
        $request->validate([
            'fcm_token' => 'required|string',
        ]);

        $request->user()->currentAccessToken()->update([
            'fcm_token' => $request->fcm_token,
        ]);

        return $this->successResponse(null, __('message.fcm_token_updated_successfully'));
    }
}
