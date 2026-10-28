<?php

namespace Tests\Feature\Api;

use App\Models\Deposit;
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

    public function test_unauthenticated_user_cannot_start_a_deposit(): void
    {
        $this->postJson('/api/add-fund', ['currency' => 'INR', 'amount' => 1000])->assertUnauthorized();
    }

    public function test_add_fund_configuration_only_exposes_public_payment_details(): void
    {
        config([
            'gateway.upigateway.mode' => 'test',
            'gateway.upigateway.modes.test.key' => 'secret-user-token',
            'gateway.upigateway.modes.test.base_url' => 'https://upi.test',
            'gateway.usd_to_inr_rate' => 100,
        ]);
        $user = $this->createUser();
        Sanctum::actingAs($user);

        $this->getJson('/api/add-fund-config')
            ->assertOk()
            ->assertJsonPath('data.provider', 'multi-gateway')
            ->assertJsonPath('data.wallet_currency', 'USD')
            ->assertJsonPath('data.exchange_rate', 100)
            ->assertDontSee('secret-user-token');
    }

    public function test_gateway_confirms_exact_inr_amount_before_crediting_usd_wallet_once(): void
    {
        config([
            'gateway.upigateway.mode' => 'test',
            'gateway.upigateway.modes.test.key' => 'secret-user-token',
            'gateway.upigateway.modes.test.base_url' => 'https://upi.test',
            'gateway.usd_to_inr_rate' => 100,
        ]);
        Http::preventStrayRequests();
        Http::fake([
            'https://upi.test/api/create_order' => Http::response([
                'status' => true,
                'data' => ['payment_url' => 'https://checkout.example/pay/test-order'],
            ]),
            'https://upi.test/api/check_order_status' => Http::response([
                'status' => true,
                'data' => ['status' => 'success', 'amount' => '1000.00', 'upi_txn_id' => 'UPI-UTR-TEST'],
            ]),
        ]);
        $user = $this->createUser();
        Sanctum::actingAs($user);

        $createResponse = $this->postJson('/api/add-fund', ['currency' => 'INR', 'amount' => '1000.00'])->assertCreated();
        $depositRef = $createResponse->json('data.deposit_ref');
        $createResponse->assertJsonPath('data.amount_usd', 10)
            ->assertJsonPath('data.amount_inr', 1000)
            ->assertJsonPath('data.payment_url', 'https://checkout.example/pay/test-order');

        $this->getJson('/api/add-fund-status?deposit_ref='.$depositRef)
            ->assertOk()
            ->assertJsonPath('data.status', 'approved');
        $this->getJson('/api/add-fund-status?deposit_ref='.$depositRef)
            ->assertOk()
            ->assertJsonPath('data.status', 'approved');

        $this->assertSame(10.0, (float) $user->fresh()->deposit_wallet);
        $this->assertSame(1, Transaction::query()->where('trx_id', $depositRef)->count());
        Http::assertSent(fn (Request $request): bool => $request->url() === 'https://upi.test/api/create_order'
            && $request['amount'] === '1000.00'
            && $request['client_txn_id'] === $depositRef);
    }

    public function test_usd_ipayment_deposit_creates_checkout_and_credits_only_after_verified_payment(): void
    {
        config([
            'gateway.ipaymentwallet.enabled' => true,
            'gateway.ipaymentwallet.mode' => 'test',
            'gateway.ipaymentwallet.modes.test.api_key' => 'ipayment-test-key',
            'gateway.ipaymentwallet.modes.test.base_url' => 'https://ipayment.test/api',
        ]);
        Http::preventStrayRequests();
        Http::fake([
            'https://ipayment.test/api/v1/create-payment' => Http::response([
                'status' => 'success',
                'data' => [
                    'transactionId' => 'IPW-ORDER-123',
                    'paymentAddress' => '0xabc123',
                    'qrUrl' => 'https://ipayment.test/qr/IPW-ORDER-123',
                ],
            ]),
            'https://ipayment.test/api/payment/check-status' => Http::response([
                'status' => 'success',
                'payment_confirmed' => true,
                'data' => ['transaction_id' => 'IPW-ORDER-123', 'amount' => '10.00'],
            ]),
        ]);
        $user = $this->createUser();
        Sanctum::actingAs($user);

        $created = $this->postJson('/api/add-fund', ['currency' => 'USD', 'amount' => '10.00'])->assertCreated()
            ->assertJsonPath('data.amount_usd', 10)
            ->assertJsonPath('data.gateway_reference', 'IPW-ORDER-123');
        $depositRef = $created->json('data.deposit_ref');
        $deposit = Deposit::query()->where('deposit_ref', $depositRef)->firstOrFail();
        $this->assertSame('ipaymentwallet', $deposit->payment_provider);
        $this->assertSame(0.0, (float) $user->fresh()->deposit_wallet);

        $this->getJson('/api/add-fund-status?deposit_ref='.$depositRef)->assertOk()->assertJsonPath('data.status', 'approved');
        $this->assertSame(10.0, (float) $user->fresh()->deposit_wallet);
        $this->assertSame(1, Transaction::query()->where('trx_id', $depositRef)->count());
        Http::assertSent(fn (Request $request): bool => $request->url() === 'https://ipayment.test/api/v1/create-payment'
            && $request['txnId'] === $depositRef
            && $request['amount'] === '10.00');
        Http::assertSent(fn (Request $request): bool => $request->url() === 'https://ipayment.test/api/payment/check-status'
            && $request->hasHeader('X-API-Key', 'ipayment-test-key'));
    }

    private function createUser(): User
    {
        $userRole = Role::query()->firstOrCreate(['slug' => 'user'], ['name' => 'User']);
        Role::query()->firstOrCreate(['slug' => 'admin'], ['name' => 'Admin']);

        return User::factory()->create(['role_id' => $userRole->id, 'mobile' => '9876543210']);
    }
}
