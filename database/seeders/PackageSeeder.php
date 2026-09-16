<?php

namespace Database\Seeders;

use App\Models\Package;
use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $packages = [
            [
                'name' => 'Starter Capital Kit',
                'min_amount' => 1000.00,
                'max_amount' => 99999.00,
                'daily_roi_percentage' => 0.15,
                'duration_days' => 730,
                'direct_bonus_percentage' => 5.00,
                'level_income_percentage' => 0.50,
                'status' => 'active',
                'description' => '0.15% Daily ROI Return for 730 Days + 5% Level 1 Direct & 0.5% Level 2-15 Team Business Level Income.',
            ],
            [
                'name' => 'Silver Growth Plan',
                'min_amount' => 100000.00,
                'max_amount' => 499999.00,
                'daily_roi_percentage' => 0.20,
                'duration_days' => 730,
                'direct_bonus_percentage' => 5.00,
                'level_income_percentage' => 0.50,
                'status' => 'active',
                'description' => '0.20% Daily ROI Return for 730 Days + 5% Level 1 Direct & 0.5% Level 2-15 Team Business Level Income.',
            ],
            [
                'name' => 'Gold Prosperity Plan',
                'min_amount' => 500000.00,
                'max_amount' => 999999.00,
                'daily_roi_percentage' => 0.25,
                'duration_days' => 730,
                'direct_bonus_percentage' => 5.00,
                'level_income_percentage' => 0.50,
                'status' => 'active',
                'description' => '0.25% Daily ROI Return for 730 Days + 5% Level 1 Direct & 0.5% Level 2-15 Team Business Level Income.',
            ],
            [
                'name' => 'Platinum Crown Club',
                'min_amount' => 1000000.00,
                'max_amount' => 10000000.00,
                'daily_roi_percentage' => 0.30,
                'duration_days' => 730,
                'direct_bonus_percentage' => 5.00,
                'level_income_percentage' => 0.50,
                'status' => 'active',
                'description' => '0.30% Daily ROI Return for 730 Days + 5% Level 1 Direct & 0.5% Level 2-15 Team Business Level Income.',
            ],
        ];

        foreach ($packages as $pkg) {
            Package::updateOrCreate(
                ['name' => $pkg['name']],
                $pkg
            );
        }
    }
}
