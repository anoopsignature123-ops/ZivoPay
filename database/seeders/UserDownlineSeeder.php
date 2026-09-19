<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserDownlineSeeder extends Seeder
{
    /**
     * Seed 150 users in a single-leg downline chain (one under another), excluding root user.
     */
    public function run(): void
    {
        // 1. Get or create Root User (Top Sponsor)
        $rootUser = User::where('email', 'root@zivopay.net')->first();

        if (! $rootUser) {
            $rootUser = User::where('referral_code', 'ZIVO-0000001')->first();
        }

        if (! $rootUser) {
            $rootUser = User::create([
                'role_id' => 2,
                'name' => 'Root User',
                'email' => 'root@zivopay.net',
                'mobile' => '9876543210',
                'referral_code' => 'ZIVO-0000001',
                'sponsor_code' => null,
                'status' => 'inactive',
                'is_subscription_active' => false,
                'subscription_activated_at' => now(),
                'deposit_wallet' => 0.00,
                'earning_wallet' => 0.00,
                'password' => Hash::make('Root@123'),
            ]);
        }

        $currentSponsorCode = $rootUser->referral_code;

        // 2. Create 150 Users sequentially (User 1 to User 150) - Each sponsored by previous user
        for ($i = 1; $i <= 150; $i++) {
            $referralCode = 'ZIVO-'.str_pad((string) (1000000 + $i), 7, '0', STR_PAD_LEFT);
            $email = "user{$i}@zivopay.net";
            $mobile = '91'.str_pad((string) $i, 8, '0', STR_PAD_LEFT);
            $name = "User {$i}";

            $user = User::updateOrCreate(
                ['email' => $email],
                [
                    'role_id' => 2,
                    'name' => $name,
                    'email' => $email,
                    'mobile' => $mobile,
                    'referral_code' => $referralCode,
                    'sponsor_code' => $currentSponsorCode,
                    'position' => 'direct',
                    'status' => 'inactive',
                    'is_subscription_active' => false,
                    'subscription_activated_at' => now(),
                    'deposit_wallet' => 0.00,
                    'earning_wallet' => 0.00,
                    'password' => Hash::make('User@123'),
                ]
            );

            // Chain sponsor_code to the newly created user's referral code
            $currentSponsorCode = $user->referral_code;
        }
    }
}
