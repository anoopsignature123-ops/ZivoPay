<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAllRoutesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_all_admin_panel_routes_load_without_errors(): void
    {
        $admin = User::create([
            'role_id' => 1,
            'name' => 'Super Admin Tester',
            'email' => 'admin_test@zivopay.com',
            'referral_code' => 'ZIVO-ADMIN100',
            'password' => bcrypt('admin123'),
        ]);

        $this->actingAs($admin);

        $routesToTest = [
            'admin.dashboard',
            'admin.users',
            'admin.users.index',
            'admin.users.create',
            'admin.deposits.index',
            'admin.network.tree',
            'admin.investments',
            'admin.packages.index',
            'admin.packages.create',
            'admin.withdrawals',
            'admin.reports.subscriptions',
            'admin.reports.subscription-direct',
            'admin.reports.subscription-team',
            'admin.reports.roi',
            'admin.reports.level-direct',
            'admin.reports.level-roi',
            'admin.reports.rewards',
            'admin.reports.deposits',
            'admin.reports.transactions',
            'admin.profile',
        ];

        foreach ($routesToTest as $routeName) {
            $response = $this->get(route($routeName));
            $response->assertStatus(200);
        }
    }
}
