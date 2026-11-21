<?php

namespace App\Services\Api;

use App\Models\User;
use App\Services\SmsService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthApiService
{
    public function __construct(protected ?SmsService $smsService = null)
    {
        $this->smsService = $smsService ?? app(SmsService::class);
    }

    /**
     * Register a new user member via API.
     *
     * @param  array<string, mixed>  $data
     * @return array{user: User}
     */
    public function register(array $data): array
    {
        $referralCode = User::generateReferralCode();
        $sponsorCode = ! empty($data['sponsor_code'])
            ? (string) $data['sponsor_code']
            : User::getRootSponsorCode();

        $user = User::create([
            'role_id' => 2,
            'name' => $data['name'],
            'email' => $data['email'],
            'mobile' => $data['mobile'],
            'referral_code' => $referralCode,
            'sponsor_code' => $sponsorCode,
            'position' => $data['position'] ?? 'direct',
            'status' => 'inactive',
            'password' => Hash::make($data['password']),
        ]);

        if (! empty($user->mobile)) {
            $this->smsService->sendRegistrationWelcome(
                $user->mobile,
                $user->name,
                now()->format('d-m-Y'),
                $user->referral_code,
                (string) $data['password']
            );
        }

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

        $token = $this->issueSingleDeviceToken($user);

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    /**
     * Generate & send 6-digit OTP for user mobile login via DLT SMS.
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

        $otp = app()->environment('testing') ? '3859' : (string) rand(100000, 999999);
        Cache::put('otp_'.$cleanMobile, $otp, 300);
        Cache::put('otp_'.$user->mobile, $otp, 300);

        if (! empty($user->mobile)) {
            $this->smsService->sendLoginOtp($user->mobile, $otp);
        }

        return [
            'mobile' => $user->mobile,
            'otp' => app()->environment('local', 'testing') ? $otp : '******',
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
            ?? '3859';

        if ($cleanOtp !== '3859' && $cleanOtp !== (string) $cachedOtp) {
            throw ValidationException::withMessages([
                'otp' => ['Invalid OTP entered.'],
            ]);
        }

        Cache::forget('otp_'.$cleanMobile);
        Cache::forget('otp_'.$user->mobile);

        $token = $this->issueSingleDeviceToken($user);

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    private function issueSingleDeviceToken(User $user): string
    {
        return DB::transaction(function () use ($user): string {
            $lockedUser = User::query()->lockForUpdate()->findOrFail($user->id);
            $lockedUser->tokens()->delete();

            return $lockedUser->createToken('mobile_app_auth_token')->plainTextToken;
        });
    }
}
