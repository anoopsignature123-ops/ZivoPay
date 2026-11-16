<?php

namespace Database\Factories;

use App\Models\LoginAudit;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LoginAudit>
 */
class LoginAuditFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'channel' => fake()->randomElement(['mobile_api', 'member_web', 'admin_web']),
            'login_method' => 'password',
            'ip_address' => fake()->ipv4(),
            'device_name' => fake()->randomElement(['Android Phone', 'iPhone', 'Desktop Browser']),
            'device_model' => fake()->randomElement(['Pixel 8', 'iPhone 15', 'Desktop']),
            'platform' => fake()->randomElement(['Android', 'iOS', 'Windows']),
            'os_version' => fake()->numerify('#.#.#'),
            'app_version' => fake()->numerify('1.#.#'),
            'user_agent' => fake()->userAgent(),
            'logged_in_at' => fake()->dateTimeThisMonth(),
        ];
    }
}
