<?php

namespace Database\Seeders;

use App\Models\Package;
use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder
{
    /**
     * Run the database seeds matching official Dex Trade PDF Presentation (Slides 8, 10, & 17).
     */
    public function run(): void
    {
        $packages = [
            [
                'name' => 'DEX TRADE STARTER',
                'min_amount' => 10.00,
                'max_amount' => 100000.00,
                'daily_roi' => 0.50,
                'duration_days' => 400,
                'total_return_multiplier' => 2.00,
                'status' => 'active',
                'description' => 'Dex Trade Package: Minimum $10 (Multiple of $10) | 0.5% Daily ROI | 400 Days | 2X Non-Working Return | 8X Working Cap',
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