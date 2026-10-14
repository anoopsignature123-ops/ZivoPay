<?php

namespace Tests\Feature\Api;

use App\Models\Deposit;
use App\Models\PaymentGatewaySetting;
use App\Models\Role;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PaymentControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_app_cannot_start_add_money_payment_and_receives_401(): void
    {
        $this->postJson('/api/add-fund', ['amount' => 500])
            ->assertUnauthorized();
    }

    public function test_dashboard_returns_public_add_money_settings_without_server_secrets(): void
    {
        PaymentGatewaySetting::query()->updateOrCreate([
            'gateway' => 'razorpay',
        ], [
            'enabled' => true,
            'mode' => 'test',
            'test_key_id' => 'rzp_test_public',
            'test_key_secret' => 'never_return_this_secret',
            'test_webhook_secret' => 'never_return_this_webhook_secret',
        ]);
        $user = $this->createUser();
        Sanctum::actingAs($user);

        $this->getJson('/api/dashboard')
            ->assertOk()
            ->assertJsonMissingPath('data.add_money');

        $this->getJson('/api/add-fund-config')
            ->assertOk()
            ->assertJsonPath('data.key_id', 'rzp_test_public')
            ->assertJsonPath('data.mode', 'test')
            ->assertJsonPath('data.enabled', true)
            ->assertJsonPath('data.currency', 'INR')
            ->assertJsonPath('data.start_url', route('api.add-fund.start'))
            ->assertJsonPath('data.verify_url', route('api.add-fund.verify'))
            ->assertJsonPath('data.status_url', route('api.add-fund.status'))
            ->assertJsonPath('data.config_url', route('api.add-fund.config'))
            ->assertJsonMissingPath('data.key_secret')
            ->assertJsonMissingPath('data.api_key_secret')
            ->assertJsonMissingPath('data.webhook_secret')
            ->assertJsonMissingPath('data.min_amount')
            ->assertJsonMissingPath('data.max_amount')
            ->assertJsonMissingPath('data.upi_id');
    }

    public function test_app_receives_add_money_checkout_details_and_pending_wallet_history(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            'https://api.razorpay.com/v1/orders' => Http::response([
                'id' => 'order_test_123',
                'amount' => 50000,
                'currency' => 'INR',
            ]),
        ]);
        $settings = PaymentGatewaySetting::query()->updateOrCreate([
            'gateway' => 'razorpay',
            'enabled' => true,
            'mode' => 'test',
            'test_key_id' => 'rzp_test_public',
            'test_key_secret' => 'test_secret_only_on_server',
        ]);
        $user = $this->createUser(['deposit_wallet' => 250]);
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/add-fund', ['amount' => '500.00']);

        $response->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Add Money payment started.')
            ->assertJsonPath('data.order_id', 'order_test_123')
            ->assertJsonPath('data.amount', 50000)
            ->assertJsonPath('data.currency', 'INR')
            ->assertJsonPath('data.key_id', 'rzp_test_public')
            ->assertJsonPath('data.mode', 'test')
            ->assertJsonPath('data.prefill.name', $user->name)
            ->assertJsonMissingPath('data.key_secret');

        $depositRef = $response->json('data.deposit_ref');
        $this->assertDatabaseHas('deposits', [
            'deposit_ref' => $depositRef,
            'user_id' => $user->id,
            'amount' => 500,
            'payment_order_id' => 'order_test_123',
            'status' => 'pending',
        ]);
        $this->assertSame(250.0, (float) $user->fresh()->deposit_wallet);

        $this->getJson('/api/fund-history')
            ->assertOk()
            ->assertJsonPath('data.deposits.0.deposit_ref', $depositRef)
            ->assertJsonPath('data.deposits.0.status', 'pending');
        $this->getJson('/api/add-fund-status?deposit_ref='.$depositRef)
            ->assertOk()
            ->assertJsonPath('data.status', 'pending')
            ->assertJsonPath('data.deposit_wallet_balance', 250);

        Http::assertSent(fn (Request $request): bool => $request->url() === 'https://api.razorpay.com/v1/orders'
            && $request['amount'] === 50000
            && $request->hasHeader('Authorization')
        );
    }

    public function test_app_payment_amount_must_be_whole_paise_and_within_limits(): void
    {
        PaymentGatewaySetting::query()->updateOrCreate([
            'gateway' => 'razorpay',
            'enabled' => true,
            'mode' => 'test',
            'test_key_id' => 'rzp_test_public',
            'test_key_secret' => 'test_secret_only_on_server',
        ]);
        $user = $this->createUser();
        Sanctum::actingAs($user);

        $this->postJson('/api/add-fund', ['amount' => 100.001])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('amount');

        $this->assertDatabaseCount('deposits', 0);
    }

    public function test_app_add_fund_validation_does_not_disclose_amount_limits(): void
    {
        PaymentGatewaySetting::query()->updateOrCreate([
            'gateway' => 'razorpay',
        ], [
            'enabled' => true,
            'mode' => 'test',
            'test_key_id' => 'rzp_test_public',
            'test_key_secret' => 'test_secret_only_on_server',
        ]);
        $user = $this->createUser();
        Sanctum::actingAs($user);

        $this->postJson('/api/add-fund', ['amount' => 99])
            ->assertUnprocessable()
            ->assertJsonPath('errors.amount.0', 'Amount is below the allowed limit.')
            ->assertDontSee('100')
            ->assertDontSee('500000');
    }

    public function test_verified_captured_app_payment_credits_wallet_once(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            'https://api.razorpay.com/v1/payments/pay_test_123' => Http::response([
                'id' => 'pay_test_123',
                'order_id' => 'order_test_123',
                'amount' => 50000,
                'currency' => 'INR',
                'status' => 'captured',
            ]),
        ]);
        PaymentGatewaySetting::query()->updateOrCreate([
            'gateway' => 'razorpay',
            'enabled' => true,
            'mode' => 'test',
            'test_key_id' => 'rzp_test_public',
            'test_key_secret' => 'test_secret_only_on_server',
        ]);
        $user = $this->createUser(['deposit_wallet' => 250]);
        $deposit = Deposit::create([
            'user_id' => $user->id,
            'deposit_ref' => 'DEP-APPTEST123',
            'amount' => 500,
            'charge' => 0,
            'final_amount' => 500,
            'payment_method' => 'Razorpay',
            'payment_provider' => 'razorpay',
            'payment_order_id' => 'order_test_123',
            'payment_metadata' => ['mode' => 'test'],
            'status' => 'pending',
        ]);
        Sanctum::actingAs($user);
        $signature = hash_hmac('sha256', 'order_test_123|pay_test_123', 'test_secret_only_on_server');
        $payload = [
            'deposit_ref' => $deposit->deposit_ref,
            'razorpay_payment_id' => 'pay_test_123',
            'razorpay_order_id' => 'order_test_123',
            'razorpay_signature' => $signature,
        ];

        $this->postJson('/api/add-fund-verify', $payload)
            ->assertOk()
            ->assertJsonPath('data.status', 'approved')
            ->assertJsonPath('data.deposit_wallet_balance', 750);
        $this->postJson('/api/add-fund-verify', $payload)
            ->assertOk()
            ->assertJsonPath('data.status', 'approved')
            ->assertJsonPath('data.deposit_wallet_balance', 750);

        $this->assertSame(750.0, (float) $user->fresh()->deposit_wallet);
        $this->assertSame(1, Transaction::where('trx_id', $deposit->deposit_ref)->count());
    }

    public function test_app_cannot_verify_another_users_payment_and_receives_404(): void
    {
        PaymentGatewaySetting::query()->updateOrCreate([
            'gateway' => 'razorpay',
            'enabled' => true,
            'mode' => 'test',
            'test_key_id' => 'rzp_test_public',
            'test_key_secret' => 'test_secret_only_on_server',
        ]);
        $owner = $this->createUser();
        $otherUser = $this->createUser();
        $deposit = Deposit::create([
            'user_id' => $owner->id,
            'deposit_ref' => 'DEP-OTHERUSER12',
            'amount' => 500,
            'charge' => 0,
            'final_amount' => 500,
            'payment_method' => 'Razorpay',
            'payment_provider' => 'razorpay',
            'payment_order_id' => 'order_other_123',
            'payment_metadata' => ['mode' => 'test'],
            'status' => 'pending',
        ]);
        Sanctum::actingAs($otherUser);

        $this->postJson('/api/add-fund-verify', [
            'deposit_ref' => $deposit->deposit_ref,
            'razorpay_payment_id' => 'pay_other_123',
            'razorpay_order_id' => 'order_other_123',
            'razorpay_signature' => 'invalid',
        ])->assertNotFound();
    }

    public function test_app_payment_status_only_returns_the_authenticated_users_deposit(): void
    {
        $owner = $this->createUser();
        $otherUser = $this->createUser();
        $deposit = Deposit::create([
            'user_id' => $owner->id,
            'deposit_ref' => 'DEP-STATUSUSER1',
            'amount' => 500,
            'charge' => 0,
            'final_amount' => 500,
            'payment_method' => 'Razorpay',
            'payment_provider' => 'razorpay',
            'payment_order_id' => 'order_status_123',
            'status' => 'pending',
        ]);
        Sanctum::actingAs($otherUser);

        $this->getJson('/api/add-fund-status?deposit_ref='.$deposit->deposit_ref)
            ->assertNotFound();
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function createUser(array $attributes = []): User
    {
        Role::query()->firstOrCreate(['slug' => 'admin'], ['name' => 'Admin']);
        Role::query()->firstOrCreate(['slug' => 'user'], ['name' => 'User']);

        return User::factory()->create($attributes);
    }
}
