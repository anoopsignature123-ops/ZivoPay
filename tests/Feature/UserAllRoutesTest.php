<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserAllRoutesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_all_user_panel_routes_load_without_errors(): void
    {
        $user = User::create([
            'role_id' => 2,
            'name' => 'Route Tester',
            'email' => 'routetester@example.com',
            'referral_code' => 'ZIVO-9990001',
            'password' => bcrypt('password123'),
            'deposit_wallet' => 5000.00,
            'earning_wallet' => 2000.00,
            'status' => 'active',
            'is_subscription_active' => true,
            'activated_at' => now(),
        ]);

        $this->actingAs($user);

        $routesToTest = [
            'user.dashboard',
            'user.wallet.transfer',
            'user.reports.deposits',
            'user.package.buy',
            'user.reports.package-history',
            'user.income.subscription-direct',
            'user.income.subscription-team',
            'user.income.roi',
            'user.income.level-roi',
            'user.investment.index',
            'user.reports.investments',
            'user.income.level-direct',
            'user.income.direct-business',
            'user.rewards.index',
            'user.income.index',
            'user.withdrawal.index',
            'user.network.direct',
            'user.network.tree',
            'user.profile',
        ];

        foreach ($routesToTest as $routeName) {
            $response = $this->get(route($routeName));
            $response->assertStatus(200);
        }
    }
}
