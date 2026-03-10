<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\SendOtpRequest;
use App\Http\Requests\Auth\VerifyOtpRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\UserResource;

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
        $result = $this->authService->verifyOtp($request->phone, $request->otp);

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
        $result = $this->authService->register($request->validated());

        if (isset($result['user'])) {
            $result['user'] = new UserResource($result['user']);
        }

        return $this->successResponse($result, __('message.registration_successfully'));
    }
}
