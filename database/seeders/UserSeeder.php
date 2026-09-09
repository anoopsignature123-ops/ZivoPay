<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds for Dex Trade.
     */
    public function run(): void
    {
        // 1. Default Super Admin User
        User::updateOrCreate(
            ['email' => 'admin@dextrade.com'],
            [
                'role_id' => 1,
                'name' => 'Super Admin',
                'email' => 'admin@dextrade.com',
                'mobile' => '1234567890',
                'referral_code' => 'DEX-ADMIN01',
                'sponsor_code' => null,
                'status' => 'active',
                'password' => Hash::make('Admin@123'),
            ]
        );

        // 2. Default Root Member User (Top Member)
        User::updateOrCreate(
            ['email' => 'root@dextrade.com'],
            [
                'role_id' => 2,
                'name' => 'Root User',
                'email' => 'root@dextrade.com',
                'mobile' => '9876543210',
                'referral_code' => 'DEX-0000001',
                'sponsor_code' => null,
                'status' => 'active',
                'deposit_wallet' => 0.00,
                'earning_wallet' => 0.00,
                'password' => Hash::make('Root@123'),
            ]
        );
    }
}
