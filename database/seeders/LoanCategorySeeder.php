<?php

namespace Database\Seeders;

use App\Models\LoanCategory;
use Illuminate\Database\Seeder;

class LoanCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['Car Loan', 'car-loan', 'Finance for new and certified pre-owned vehicles.', 8.40, 50000, 5000000, 7, 'car'],
            ['Home Loan', 'home-loan', 'Funding for home purchase, construction or improvement.', 8.25, 100000, 50000000, 30, 'house'],
            ['Bike Loan', 'bike-loan', 'Financing for two-wheeler purchases.', 9.99, 25000, 1500000, 5, 'bike'],
            ['Personal Loan', 'personal-loan', 'Flexible funding for personal expenses.', 10.25, 25000, 2500000, 7, 'wallet'],
            ['Business Loan', 'business-loan', 'Working capital and business expansion finance.', 11.50, 100000, 10000000, 10, 'building'],
            ['Government Subsidy Loan', 'government-subsidy-loan', 'Government supported lending schemes.', 7.50, 25000, 5000000, 10, 'landmark'],
            ['Education Loan', 'education-loan', 'Funding for higher education in India or overseas.', 8.50, 50000, 15000000, 15, 'graduation-cap'],
            ['Gold Loan', 'gold-loan', 'Credit against pledged gold jewellery.', 8.80, 10000, 2500000, 5, 'coins'],
            ['Agriculture Loan', 'agriculture-loan', 'Credit for farming, equipment and agricultural needs.', 7.00, 25000, 5000000, 10, 'wheat'],
            ['Other Loan', 'other-loan', 'Other eligible secured and unsecured loan needs.', 9.50, 25000, 10000000, 15, 'credit-card'],
        ];
        foreach ($categories as [$name, $slug, $description, $rate, $minimum, $maximum, $tenure, $icon]) {
            LoanCategory::query()->updateOrCreate(['slug' => $slug], [
                'name' => $name, 'description' => $description, 'indicative_interest_rate' => $rate,
                'minimum_amount' => $minimum, 'maximum_amount' => $maximum,
                'maximum_tenure_years' => $tenure, 'icon' => $icon, 'is_active' => true,
            ]);
        }
    }
}
