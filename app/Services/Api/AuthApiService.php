<?php

namespace App\Services\Api;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
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
     * Accepts login_id as User ID (referral_code), Email, or Mobile with Password or OTP.
     *
     * @return array{user: User, token: string}
     *
     * @throws ValidationException
     */
    public function login(string $loginId, ?string $password = null, ?string $otp = null): array
    {
        if (! empty($otp)) {
            return $this->verifyOtp($loginId, $otp);
        }

        $user = User::where(function ($query) use ($loginId) {
            $query->where('referral_code', $loginId)
                ->orWhere('email', $loginId)
                ->orWhere('mobile', $loginId);
        })->first();

        if (! $user || empty($password) || ! Hash::check($password, $user->password)) {
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

    /**
     * Generate & send default OTP (1111) for user mobile login.
     *
     * @return array{mobile: string, otp: string}
     *
     * @throws ValidationException
     */
    public function sendOtp(string $mobile): array
    {
        $cleanMobile = trim($mobile);

        $user = User::where('mobile', $cleanMobile)
            ->orWhere('referral_code', $cleanMobile)
            ->orWhere('email', $cleanMobile)
            ->first();

        if (! $user) {
            throw ValidationException::withMessages([
                'mobile' => ['No registered account found with this mobile number.'],
            ]);
        }

        $defaultOtp = '1111';
        Cache::put('otp_'.$cleanMobile, $defaultOtp, 600);
        Cache::put('otp_'.$user->mobile, $defaultOtp, 600);

        return [
            'mobile' => $user->mobile,
            'otp' => $defaultOtp,
        ];
    }

    /**
     * Verify Mobile OTP and issue Sanctum token for login.
     *
     * @return array{user: User, token: string}
     *
     * @throws ValidationException
     */
    public function verifyOtp(string $mobile, string $otp): array
    {
        $cleanMobile = trim($mobile);
        $cleanOtp = trim($otp);

        $user = User::where('mobile', $cleanMobile)
            ->orWhere('referral_code', $cleanMobile)
            ->orWhere('email', $cleanMobile)
            ->first();

        if (! $user) {
            throw ValidationException::withMessages([
                'mobile' => ['No registered account found with this mobile number.'],
            ]);
        }

        $cachedOtp = Cache::get('otp_'.$cleanMobile)
            ?? Cache::get('otp_'.$user->mobile)
            ?? '1111';

        if ($cleanOtp !== '1111' && $cleanOtp !== (string) $cachedOtp) {
            throw ValidationException::withMessages([
                'otp' => ['Invalid OTP entered. Please enter 1111.'],
            ]);
        }

        Cache::forget('otp_'.$cleanMobile);
        Cache::forget('otp_'.$user->mobile);

        $token = $user->createToken('mobile_app_auth_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }
}
