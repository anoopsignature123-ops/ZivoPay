<?php

namespace Tests\Feature\Api;

use App\Models\Recharge;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RechargeApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_fetch_operators_and_circles_list(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/recharge/operators');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'data' => [
                    'operators' => ['mobile', 'dth', 'fastag', 'electricity'],
                    'circles',
                ],
            ]);
    }

    public function test_user_can_execute_successful_recharge(): void
    {
        Http::fake([
            'https://business.a1topup.com/recharge/api*' => Http::response([
                'txid' => 'TXN998877',
                'status' => 'Success',
                'opid' => 'OPID12345',
                'number' => '9800000000',
                'amount' => '299',
                'orderid' => 'RCG-TEST1',
            ], 200),
        ]);

        $user = User::factory()->create([
            'deposit_wallet' => 1000.00,
        ]);
        Sanctum::actingAs($user);

        $payload = [
            'operator_code' => 'A',
            'circle_code' => '10',
            'number' => '9800000000',
            'amount' => 299.00,
            'service_type' => 'mobile',
        ];

        $response = $this->postJson('/api/recharge/do', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data' => [
                    'deposit_wallet_balance' => 701.00,
                ],
            ]);

        $this->assertDatabaseHas('recharges', [
            'user_id' => $user->id,
            'number' => '9800000000',
            'amount' => 299.00,
            'status' => 'success',
            'txid' => 'TXN998877',
            'opid' => 'OPID12345',
        ]);

        $this->assertEquals(701.00, (float) $user->fresh()->deposit_wallet);
    }

    public function test_recharge_fails_due_to_insufficient_wallet_balance(): void
    {
        $user = User::factory()->create([
            'deposit_wallet' => 50.00, // Insufficient balance for ₹199 recharge
        ]);
        Sanctum::actingAs($user);

        $payload = [
            'operator_code' => 'RC',
            'number' => '9988776655',
            'amount' => 199.00,
        ];

        $response = $this->postJson('/api/recharge/do', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['amount']);

        $this->assertEquals(50.00, (float) $user->fresh()->deposit_wallet);
    }

    public function test_failed_gateway_recharge_auto_refunds_wallet(): void
    {
        Http::fake([
            'https://business.a1topup.com/recharge/api*' => Http::response([
                'txid' => 'TXNFAIL001',
                'status' => 'Failure',
                'message' => 'Operator server down',
            ], 200),
        ]);

        $user = User::factory()->create([
            'deposit_wallet' => 500.00,
        ]);
        Sanctum::actingAs($user);

        $payload = [
            'operator_code' => 'V',
            'number' => '9111111111',
            'amount' => 200.00,
        ];

        $response = $this->postJson('/api/recharge/do', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data' => [
                    'deposit_wallet_balance' => 500.00, // Deducted 200 then refunded 200 = 500
                ],
            ]);

        $this->assertDatabaseHas('recharges', [
            'user_id' => $user->id,
            'number' => '9111111111',
            'amount' => 200.00,
            'status' => 'refunded',
        ]);

        $this->assertEquals(500.00, (float) $user->fresh()->deposit_wallet);
    }

    public function test_user_can_fetch_recharge_history(): void
    {
        $user = User::factory()->create([
            'deposit_wallet' => 1000.00,
        ]);
        Sanctum::actingAs($user);

        Recharge::create([
            'user_id' => $user->id,
            'order_id' => 'RCG-HIST001',
            'service_type' => 'mobile',
            'operator_code' => 'A',
            'operator_name' => 'Airtel',
            'number' => '9800000000',
            'amount' => 199.00,
            'status' => 'success',
            'txid' => 'TX1',
        ]);

        Recharge::create([
            'user_id' => $user->id,
            'order_id' => 'RCG-HIST002',
            'service_type' => 'dth',
            'operator_code' => 'TTV',
            'operator_name' => 'Tata Play',
            'number' => '1002003004',
            'amount' => 500.00,
            'status' => 'refunded',
            'txid' => 'TX2',
        ]);

        $response = $this->getJson('/api/recharge/history');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'stats' => [
                        'total_spent' => 199.00,
                        'total_success' => 1,
                        'total_failed' => 1,
                        'current_deposit_wallet_balance' => 1000.00,
                    ],
                ],
            ]);

        $this->assertCount(2, $response->json('data.recharges'));
    }

    public function test_user_can_fetch_recharge_plans_and_home_dashboard(): void
    {
        $user = User::factory()->create([
            'deposit_wallet' => 5248.50,
        ]);
        Sanctum::actingAs($user);

        // Test Recharge Plans API
        $plansResponse = $this->getJson('/api/recharge/plans?operator_code=RC');

        $plansResponse->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.operator_code', 'RC')
            ->assertJsonStructure(['data' => ['recommended_plans', 'dth_quick_amounts']]);

        // Test Home Dashboard API matching UI
        $dashResponse = $this->getJson('/api/dashboard');

        $dashResponse->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.user.deposit_wallet', 5248.50)
            ->assertJsonStructure([
                'data' => [
                    'user' => ['id', 'deposit_wallet', 'formatted_balance'],
                    'special_offers',
                    'services' => ['recharge_and_topup', 'bills_and_utilities', 'travel_and_tickets', 'entertainment', 'financial_services'],
                    'recent_transactions',
                ],
            ]);
    }

    public function test_user_can_fetch_circles_list_and_auto_detect_operator(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        // Test Circles API
        $circlesResponse = $this->getJson('/api/recharge/circles');

        $circlesResponse->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['data' => ['circles']]);

        // Test GPay Style Auto-Detect Operator API for Airtel (9810xxxxxx - Delhi)
        $detectAirtel = $this->getJson('/api/recharge/fetch-operator?number=9810123456');
        $detectAirtel->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.operator_code', 'A')
            ->assertJsonPath('data.operator_name', 'Airtel')
            ->assertJsonPath('data.circle_code', '5')
            ->assertJsonPath('data.circle_name', 'Delhi');

        // Test Auto-Detect for Vi (9820xxxxxx - Mumbai)
        $detectVi = $this->getJson('/api/recharge/fetch-operator?number=9820123456');
        $detectVi->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.operator_code', 'V')
            ->assertJsonPath('data.operator_name', 'Vi')
            ->assertJsonPath('data.circle_code', '3')
            ->assertJsonPath('data.circle_name', 'Mumbai');

        // Test Auto-Detect for BSNL (9415xxxxxx - UP East)
        $detectBsnl = $this->getJson('/api/recharge/fetch-operator?number=9415123456');
        $detectBsnl->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.operator_code', 'BT')
            ->assertJsonPath('data.operator_name', 'BSNL')
            ->assertJsonPath('data.circle_code', '10')
            ->assertJsonPath('data.circle_name', 'Uttar Pradesh East');

        // Test Auto-Detect for Jio (7000xxxxxx)
        $detectJio = $this->getJson('/api/recharge/fetch-operator?number=7000123456');
        $detectJio->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.operator_code', 'RC')
            ->assertJsonPath('data.operator_name', 'Jio');
    }

    public function test_user_can_fetch_operators_by_category(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $responseMobile = $this->getJson('/api/recharge/operators/mobile');
        $responseMobile->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.category', 'mobile');

        $responseDth = $this->getJson('/api/recharge/operators/dth');
        $responseDth->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.category', 'dth');

        $responseElectricity = $this->getJson('/api/recharge/operators/electricity');
        $responseElectricity->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.category', 'electricity');
    }

    public function test_user_can_execute_recharge_via_dedicated_service_endpoints(): void
    {
        Http::fake([
            'https://business.a1topup.com/recharge/api*' => Http::response([
                'txid' => 'TXN-DEDICATED-123',
                'status' => 'Success',
                'opid' => 'OPID-DEDICATED',
                'number' => '1234567890',
                'amount' => '500',
                'orderid' => 'RCG-DEDICATED-1',
            ], 200),
        ]);

        $user = User::factory()->create([
            'deposit_wallet' => 2000.00,
        ]);
        Sanctum::actingAs($user);

        // 1. Mobile Dedicated API
        $resMobile = $this->postJson('/api/recharge/mobile', [
            'operator_code' => 'A',
            'circle_code' => '10',
            'number' => '9800000000',
            'amount' => 299.00,
        ]);
        $resMobile->assertStatus(201)->assertJsonPath('success', true);

        // 2. DTH Dedicated API
        $resDth = $this->postJson('/api/recharge/dth', [
            'operator_code' => 'ATV',
            'number' => '3001234567',
            'amount' => 450.00,
        ]);
        $resDth->assertStatus(201)->assertJsonPath('success', true);

        // 3. Electricity Dedicated API
        $resElec = $this->postJson('/api/recharge/electricity', [
            'operator_code' => 'BSES',
            'number' => '100200300',
            'amount' => 500.00,
        ]);
        $resElec->assertStatus(201)->assertJsonPath('success', true);

        // 4. FASTag Dedicated API
        $resFastag = $this->postJson('/api/recharge/fastag', [
            'operator_code' => 'ICF',
            'number' => 'MH01AB1234',
            'amount' => 300.00,
        ]);
        $resFastag->assertStatus(201)->assertJsonPath('success', true);
    }

    public function test_user_can_fetch_transaction_details_by_id(): void
    {
        $user = User::factory()->create([
            'deposit_wallet' => 1000.00,
        ]);
        Sanctum::actingAs($user);

        $t = Transaction::create([
            'user_id' => $user->id,
            'trx_id' => 'TXN99887766',
            'type' => 'recharge',
            'amount' => 239.00,
            'charge' => 0.00,
            'post_balance' => 761.00,
            'description' => 'Mobile Recharge for 9876543210 (Jio)',
        ]);

        Recharge::create([
            'user_id' => $user->id,
            'order_id' => 'TXN99887766',
            'service_type' => 'mobile',
            'operator_code' => 'RC',
            'operator_name' => 'Jio',
            'number' => '9876543210',
            'amount' => 239.00,
            'status' => 'success',
            'txid' => 'GW998877',
            'opid' => 'OP12345',
        ]);

        $response = $this->getJson("/api/transaction/{$t->id}");

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.id', $t->id)
            ->assertJsonPath('data.trx_id', 'TXN99887766')
            ->assertJsonPath('data.amount', 239)
            ->assertJsonPath('data.recharge_details.operator_name', 'Jio')
            ->assertJsonPath('data.recharge_details.number', '9876543210');
    }
}
