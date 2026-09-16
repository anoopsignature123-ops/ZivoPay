<?php

namespace Tests\Feature;

use App\Models\Deposit;
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
            'user.deposit.index',
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

    public function test_user_can_submit_add_fund_request_with_validation(): void
    {
        $user = User::create([
            'role_id' => 2,
            'name' => 'Fund User',
            'email' => 'funduser@example.com',
            'referral_code' => 'ZIVO-8880001',
            'password' => bcrypt('password123'),
            'deposit_wallet' => 0.00,
        ]);

        $this->actingAs($user);

        // Submit Add Fund Request
        $response = $this->post(route('user.deposit.store'), [
            'amount' => 5000.00,
            'payment_method' => 'UPI',
            'trx_hash' => 'UTR1234567890',
        ]);

        $response->assertRedirect(route('user.deposit.index'));

        $this->assertDatabaseHas('deposits', [
            'user_id' => $user->id,
            'amount' => 5000.00,
            'payment_method' => 'UPI',
            'trx_hash' => 'UTR1234567890',
            'status' => 'approved',
        ]);

        $this->assertEquals(5000.00, $user->fresh()->deposit_wallet);
    }

    public function test_admin_can_approve_deposit_request_and_credit_user_fund_wallet(): void
    {
        $admin = User::create([
            'role_id' => 1,
            'name' => 'Admin Approver',
            'email' => 'adminapprover@example.com',
            'referral_code' => 'ZIVO-ADMIN02',
            'password' => bcrypt('password123'),
        ]);

        $user = User::create([
            'role_id' => 2,
            'name' => 'Deposit User',
            'email' => 'depuser@example.com',
            'referral_code' => 'ZIVO-7770001',
            'password' => bcrypt('password123'),
            'deposit_wallet' => 500.00,
        ]);

        $deposit = Deposit::create([
            'user_id' => $user->id,
            'deposit_ref' => 'DEP-TEST12345',
            'amount' => 2500.00,
            'charge' => 0.00,
            'final_amount' => 2500.00,
            'payment_method' => 'UPI',
            'trx_hash' => 'UTR9876543210',
            'status' => 'pending',
        ]);

        $this->actingAs($admin);

        // Admin approves deposit
        $response = $this->post(route('admin.deposits.approve', $deposit));
        $response->assertRedirect();

        $this->assertDatabaseHas('deposits', [
            'id' => $deposit->id,
            'status' => 'approved',
        ]);

        // Assert user's deposit_wallet is credited by ₹2,500 (Initial ₹500 + ₹2,500 = ₹3,000)
        $this->assertEquals(3000.00, $user->fresh()->deposit_wallet);

        // Assert transaction log created
        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'type' => 'deposit',
            'amount' => 2500.00,
        ]);
    }
}
