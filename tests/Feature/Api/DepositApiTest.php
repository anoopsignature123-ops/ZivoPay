<?php

namespace Tests\Feature\Api;

use App\Models\Deposit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DepositApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_submit_add_fund_request_successfully(): void
    {
        $user = User::factory()->create([
            'deposit_wallet' => 100.00,
        ]);

        Sanctum::actingAs($user);

        $payload = [
            'amount' => 500.00,
            'payment_method' => 'UPI',
            'trx_hash' => 'UTR998877665544',
            'remark' => 'Test Add Fund',
        ];

        $response = $this->postJson('/api/add-fund', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Success! ₹500.00 has been credited to your Fund Wallet.',
                'data' => [
                    'deposit_wallet_balance' => 600.00,
                ],
            ]);

        $this->assertDatabaseHas('deposits', [
            'user_id' => $user->id,
            'amount' => 500.00,
            'payment_method' => 'UPI',
            'trx_hash' => 'UTR998877665544',
            'status' => 'approved',
        ]);

        $this->assertEquals(600.00, (float) $user->fresh()->deposit_wallet);
    }

    public function test_user_can_submit_add_fund_with_proof_file(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $file = UploadedFile::fake()->image('payment_receipt.jpg');

        $payload = [
            'amount' => 1000.00,
            'payment_method' => 'USDT',
            'trx_hash' => '0x1234567890abcdef',
            'proof_file' => $file,
        ];

        $response = $this->postJson('/api/add-fund', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
            ]);

        $deposit = Deposit::where('trx_hash', '0x1234567890abcdef')->first();
        $this->assertNotNull($deposit);
        $this->assertNotNull($deposit->proof_file);

        Storage::disk('public')->assertExists($deposit->proof_file);
    }

    public function test_add_fund_fails_validation_for_min_amount_and_duplicate_trx_hash(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        // Min deposit validation test
        $response = $this->postJson('/api/add-fund', [
            'amount' => 10.00, // Below min 100
            'payment_method' => 'UPI',
            'trx_hash' => 'UTR1122334455',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['amount']);

        // Duplicate UTR / hash test
        Deposit::create([
            'user_id' => $user->id,
            'deposit_ref' => 'DEP-TEST12345',
            'amount' => 200.00,
            'charge' => 0.00,
            'final_amount' => 200.00,
            'payment_method' => 'UPI',
            'trx_hash' => 'EXISTING_UTR_123',
            'status' => 'approved',
        ]);

        $dupResponse = $this->postJson('/api/add-fund', [
            'amount' => 200.00,
            'payment_method' => 'UPI',
            'trx_hash' => 'EXISTING_UTR_123',
        ]);

        $dupResponse->assertStatus(422)
            ->assertJsonValidationErrors(['trx_hash']);
    }

    public function test_user_can_fetch_deposit_history_and_stats(): void
    {
        $user = User::factory()->create([
            'deposit_wallet' => 1500.00,
        ]);

        Sanctum::actingAs($user);

        Deposit::create([
            'user_id' => $user->id,
            'deposit_ref' => 'DEP-001',
            'amount' => 1000.00,
            'charge' => 0.00,
            'final_amount' => 1000.00,
            'payment_method' => 'UPI',
            'trx_hash' => 'HASH001',
            'status' => 'approved',
        ]);

        Deposit::create([
            'user_id' => $user->id,
            'deposit_ref' => 'DEP-002',
            'amount' => 500.00,
            'charge' => 0.00,
            'final_amount' => 500.00,
            'payment_method' => 'USDT',
            'trx_hash' => 'HASH002',
            'status' => 'pending',
        ]);

        $response = $this->getJson('/api/fund-history');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'stats' => [
                        'total_approved' => 1000.00,
                        'total_pending' => 500.00,
                        'total_count' => 2,
                        'current_deposit_wallet_balance' => 1500.00,
                    ],
                ],
            ]);

        $this->assertCount(2, $response->json('data.deposits'));
    }
}
