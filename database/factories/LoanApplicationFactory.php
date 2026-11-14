<?php

namespace Database\Factories;

use App\Models\LoanApplication;
use App\Models\LoanCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<LoanApplication>
 */
class LoanApplicationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'application_number' => 'ZIVO-LN-'.now()->format('Ymd').'-'.Str::upper(Str::random(6)),
            'user_id' => User::factory(), 'loan_category_id' => LoanCategory::factory(), 'applicant_name' => fake()->name(),
            'date_of_birth' => fake()->dateTimeBetween('-65 years', '-21 years')->format('Y-m-d'), 'mobile' => fake()->numerify('##########'),
            'email' => fake()->safeEmail(), 'loan_amount' => fake()->numberBetween(100000, 5000000), 'tenure_years' => fake()->numberBetween(1, 20),
            'preferred_lender' => fake()->company(), 'status' => 'submitted', 'declaration_accepted' => true, 'submitted_at' => now(),
        ];
    }
}
