<?php

namespace App\Services\Api;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthApiService
{
    /**
     * Register a new user member via API.
     *
     * @param  array<string, mixed>  $data
     * @return array{user: User}
     */
    public function register(array $data): array
    {
        $referralCode = User::generateReferralCode();

        $user = User::create([
            'role_id' => 2,
            'name' => $data['name'],
            'email' => $data['email'],
            'mobile' => $data['mobile'],
            'referral_code' => $referralCode,
            'sponsor_code' => $data['sponsor_code'],
            'position' => $data['position'] ?? 'direct',
            'status' => 'inactive',
            'password' => Hash::make($data['password']),
        ]);

        return [
            'user' => $user,
        ];
    }

    /**
     * Authenticate user credentials and issue API Bearer token.
     * Accepts login_id as User ID (referral_code), Email, or Mobile.
     *
     * @return array{user: User, token: string}
     *
     * @throws ValidationException
     */
    public function login(string $loginId, string $password): array
    {
        $user = User::where(function ($query) use ($loginId) {
            $query->where('referral_code', $loginId)
                ->orWhere('email', $loginId)
                ->orWhere('mobile', $loginId);
        })->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'user_id' => ['Invalid login credentials provided. Please check your ID/Email/Mobile and password.'],
            ]);
        }

        $token = $user->createToken('mobile_app_auth_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }
}
