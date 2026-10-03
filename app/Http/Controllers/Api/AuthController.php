<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\RegisterRequest;
use App\Http\Resources\Api\UserResource;
use App\Models\User;
use App\Services\Api\AuthApiService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    use ApiResponse;

    /**
     * Mobile App User Registration API.
     */
    public function register(RegisterRequest $request, AuthApiService $authService): JsonResponse
    {
        try {
            $result = $authService->register($request->validated());

            return $this->successResponse([
                'user' => new UserResource($result['user']),
            ], 'Registration successful! Please log in with your User ID and password.', 201);
        } catch (\Exception $e) {
            return $this->errorResponse('Registration failed: '.$e->getMessage(), 500);
        }
    }

    /**
     * Mobile App User Login API.
     * Login via User ID (referral code), Email, or Mobile + Password.
     */
    public function login(LoginRequest $request, AuthApiService $authService): JsonResponse
    {
        try {
            $loginId = (string) ($request->input('login_id') ?? $request->input('user_id') ?? $request->input('email') ?? $request->input('mobile'));

            $result = $authService->login(
                $loginId,
                $request->input('password')
            );

            return $this->successResponse([
                'user' => new UserResource($result['user']),
                'token' => $result['token'],
                'token_type' => 'Bearer',
            ], 'Login successful!');
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->errors(), 'Authentication failed.');
        } catch (\Exception $e) {
            return $this->errorResponse('Login failed: '.$e->getMessage(), 500);
        }
    }

    /**
     * Check & Verify Sponsor Name by Referral Code or Mobile API.
     */
    public function checkSponsor(Request $request, ?string $code = null): JsonResponse
    {
        $sponsorCode = trim((string) ($code ?? $request->get('sponsor_code') ?? $request->get('code')));

        if (empty($sponsorCode)) {
            return $this->errorResponse('Please provide a sponsor code.', 422);
        }

        $userModel = config('auth.providers.users.model', User::class);
        $sponsor = $userModel::where('referral_code', $sponsorCode)
            ->orWhere('mobile', $sponsorCode)
            ->first();

        if (! $sponsor) {
            return $this->errorResponse('Invalid sponsor code. No user found with this referral code.', 404);
        }

        return $this->successResponse([
            'sponsor' => [
                'id' => $sponsor->id,
                'name' => $sponsor->name,
                'referral_code' => $sponsor->referral_code,
                'mobile' => $sponsor->mobile,
                'status' => ucfirst($sponsor->status),
            ],
        ], 'Sponsor verified successfully.');
    }

    /**
     * Revoke Current Access Token / Logout API.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()?->delete();

        return $this->successResponse(null, 'Successfully logged out.');
    }
}
