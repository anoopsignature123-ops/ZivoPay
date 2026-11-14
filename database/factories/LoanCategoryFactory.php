<?php

namespace Database\Factories;

use App\Models\LoanCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LoanCategory>
 */
class LoanCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return ['name' => str($name)->title()->toString(), 'slug' => str($name)->slug()->toString(), 'description' => fake()->sentence(), 'indicative_interest_rate' => fake()->randomFloat(2, 5, 18), 'minimum_amount' => 50000, 'maximum_amount' => 5000000, 'maximum_tenure_years' => 20, 'sort_order' => 0, 'is_active' => true];
    }
}
