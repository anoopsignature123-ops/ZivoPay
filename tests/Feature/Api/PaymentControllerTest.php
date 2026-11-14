<?php

namespace Tests\Feature\Api;

use App\Models\Deposit;
use App\Models\Role;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PaymentControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_start_a_deposit(): void
    {
        $this->postJson('/api/add-fund', ['currency' => 'INR', 'amount' => 1000])->assertUnauthorized();
    }

    public function test_gateway_return_ignores_signed_callback_parameters_and_shows_success_only_after_approval(): void
    {
        $user = $this->createUser();
        $deposit = Deposit::query()->create([
            'user_id' => $user->id,
            'deposit_ref' => 'DEP-RETURN-TEST',
            'amount' => 10,
            'charge' => 0,
            'final_amount' => 1000,
            'amount_inr' => 1000,
            'conversion_rate' => 100,
            'payment_method' => 'UPIGateway UPI (INR)',
            'payment_provider' => 'upigateway',
            'provider_mode' => 'single',
            'status' => 'approved',
        ]);
        $gatewayReturnUrl = URL::temporarySignedRoute(
            'app.deposit.upi.return',
            now()->addMinutes(15),
            ['deposit' => $deposit->id],
        );

        $callbackResponse = $this->get($gatewayReturnUrl.'&'.http_build_query([
            'amount' => '1000.00',
            'client_txn_id' => $deposit->deposit_ref,
            'createdAt' => '2026-11-14T12:24:24Z',
            'customer_email' => $user->email,
            'customer_mobile' => '9876543210',
            'customer_name' => $user->name,
            'customer_vpa' => 'customer@upi',
            'id' => 'gateway-order-1',
            'p_info' => 'Wallet Deposit',
            'redirect_url' => 'https://zivopay.net/app/deposit/upi/1/return',
            'remark' => 'Payment complete',
            'status' => 'success',
            'txnAt' => '2026-11-14',
            'txn_id' => '193538203',
            'udf1' => (string) $user->id,
            'udf2' => '',
            'udf3' => '',
            'upi_txn_id' => '193538203',
        ]));
        $callbackResponse->assertRedirect();
        $this->assertSame('/app/deposit/upi/'.$deposit->id.'/result', parse_url($callbackResponse->headers->get('Location'), PHP_URL_PATH));
        $this->get($callbackResponse->headers->get('Location'))
            ->assertOk()
            ->assertSee('Payment successful')
            ->assertSee($deposit->deposit_ref);

        $successUrl = URL::temporarySignedRoute(
            'app.deposit.upi.result',
            now()->addMinutes(15),
            ['deposit' => $deposit->id],
        );
        $this->get($successUrl)
            ->assertOk()
            ->assertSee('Added to your Fund Wallet')
            ->assertDontSee('Back to ZIVO PAY');
    }

    public function test_add_fund_configuration_only_exposes_public_payment_details(): void
    {
        config([
            'gateway.upigateway.key' => 'secret-user-token',
            'gateway.upigateway.base_url' => 'https://upi.test',
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
            'gateway.upigateway.key' => 'secret-user-token',
            'gateway.upigateway.base_url' => 'https://upi.test',
            'gateway.usd_to_inr_rate' => 100,
        ]);
        Http::preventStrayRequests();
        $statusCheckCount = 0;
        Http::fake([
            'https://upi.test/api/create_order' => Http::response([
                'status' => true,
                'data' => ['payment_url' => 'https://checkout.example/pay/test-order'],
            ]),
            'https://upi.test/api/check_order_status' => function (Request $request) use (&$statusCheckCount) {
                $statusCheckCount++;
                $status = $statusCheckCount === 1 ? 'scanning' : 'success';
                $providerDepositRef = $statusCheckCount === 2 ? 'DEP-OTHER-ORDER' : $request['client_txn_id'];

                return Http::response([
                    'status' => true,
                    'data' => [
                        'status' => $status,
                        'client_txn_id' => $providerDepositRef,
                        'amount' => '1000.00',
                        'upi_txn_id' => 'UPI-UTR-TEST',
                    ],
                ]);
            },
        ]);
        $user = $this->createUser();
        Sanctum::actingAs($user);

        $createResponse = $this->postJson('/api/add-fund', ['currency' => 'INR', 'amount' => '1000.00'])->assertCreated();
        $depositRef = $createResponse->json('data.deposit_ref');
        $depositId = $createResponse->json('data.deposit_id');
        $webviewUrl = $createResponse->json('data.webview_url');
        $webviewStatusUrl = $createResponse->json('data.webview_status_url');
        $createResponse->assertJsonPath('data.amount_usd', 10)
            ->assertJsonPath('data.amount_inr', 1000);
        $this->assertSame($webviewUrl, $createResponse->json('data.payment_url'));
        $this->assertStringContainsString('/app/deposit/upi/'.$depositId.'?', $webviewUrl);
        $this->assertStringContainsString('/app/deposit/upi/'.$depositId.'/status?', $webviewStatusUrl);

        $otherUser = $this->createUser();
        Sanctum::actingAs($otherUser);
        $this->getJson('/api/add-fund-status?deposit_ref='.$depositRef)->assertNotFound();
        Sanctum::actingAs($user);

        $this->postJson('/api/webhook/upigateway', [
            'client_txn_id' => $depositRef,
            'amount' => '1000.00',
            'status' => 'success',
        ])->assertAccepted();
        $this->assertSame(0.0, (float) $user->fresh()->deposit_wallet);
        $this->assertDatabaseHas('deposits', ['deposit_ref' => $depositRef, 'status' => 'pending']);

        $this->get('/app/deposit/upi/'.$depositId)->assertForbidden();
        $this->get(str_replace('signature=', 'signature=invalid', $webviewUrl))->assertForbidden();
        $this->get($webviewUrl)
            ->assertOk()
            ->assertSee('Scan to add funds')
            ->assertSee('window.location.replace(resultUrl)');
        $this->assertSame(0.0, (float) $user->fresh()->deposit_wallet);
        $this->assertDatabaseHas('deposits', ['deposit_ref' => $depositRef, 'status' => 'pending']);
        $this->getJson($webviewStatusUrl)
            ->assertOk()
            ->assertJsonPath('status', 'approved');

        $this->getJson('/api/add-fund-status?deposit_ref='.$depositRef)
            ->assertOk()
            ->assertJsonPath('data.status', 'approved');
        $this->getJson('/api/add-fund-status?deposit_ref='.$depositRef)
            ->assertOk()
            ->assertJsonPath('data.status', 'approved');

        $this->assertSame(10.0, (float) $user->fresh()->deposit_wallet);
        $this->assertSame(1, Transaction::query()->where('trx_id', $depositRef)->count());
        $gatewayReturnUrl = URL::temporarySignedRoute(
            'app.deposit.upi.return',
            now()->addMinutes(15),
            ['deposit' => $depositId],
        );
        $callbackResponse = $this->get($gatewayReturnUrl.'&'.http_build_query([
            'client_txn_id' => $depositRef,
            'status' => 'success',
            'txn_id' => '193538203',
        ]));
        $callbackResponse->assertRedirect();
        $this->get($callbackResponse->headers->get('Location'))
            ->assertOk()
            ->assertSee('Payment successful')
            ->assertSee($depositRef);
        Http::assertSent(fn (Request $request): bool => $request->url() === 'https://upi.test/api/create_order'
            && $request['amount'] === '1000.00'
            && $request['client_txn_id'] === $depositRef
            && parse_url($request['redirect_url'], PHP_URL_PATH) === '/app/deposit/upi/'.$depositId.'/return'
            && str_contains((string) parse_url($request['redirect_url'], PHP_URL_QUERY), 'signature='));
    }

    public function test_usd_ipayment_deposit_creates_checkout_and_credits_only_after_verified_payment(): void
    {
        config([
            'gateway.ipaymentwallet.enabled' => true,
            'gateway.ipaymentwallet.api_key' => 'ipayment-test-key',
            'gateway.ipaymentwallet.url' => 'https://ipayment.test/api',
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
