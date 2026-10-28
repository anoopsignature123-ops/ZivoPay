<?php

namespace App\Services;

use App\Models\Deposit;
use App\Models\Transaction;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class UpigatewayService
{
    public function createOrder(Deposit $deposit, ?string $customerMobile = null): string
    {
        $gatewayConfig = $this->gatewayConfig($deposit->provider_mode);
        $token = (string) ($gatewayConfig['key'] ?? '');
        $baseUrl = (string) ($gatewayConfig['base_url'] ?? '');
        $caBundle = (string) config('gateway.upigateway.ca_bundle', '');
        if ($token === '' || $baseUrl === '') {
            throw new RuntimeException('UPIGateway is not configured.');
        }
        $mobile = preg_replace('/\D+/', '', $customerMobile ?? (string) $deposit->user->mobile);
        if (strlen($mobile) === 12 && str_starts_with($mobile, '91')) {
            $mobile = substr($mobile, 2);
        }
        if (! is_string($mobile) || ! preg_match('/^[6-9]\d{9}$/', $mobile)) {
            throw new RuntimeException('Add a valid 10-digit Indian mobile number to your profile before paying.');
        }

        try {
            $response = Http::asJson()->acceptJson()->withOptions(['verify' => $caBundle !== '' ? $caBundle : true])->connectTimeout(5)->timeout(20)->post(
                $this->endpoint('create_order', $baseUrl),
                [
                    'key' => $token,
                    'client_txn_id' => $deposit->deposit_ref,
                    'amount' => number_format((float) $deposit->amount_inr, 2, '.', ''),
                    'p_info' => 'Wallet Deposit',
                    'customer_name' => $deposit->user->name ?? 'Member',
                    'customer_email' => $deposit->user->email ?? '',
                    'customer_mobile' => $mobile,
                    'redirect_url' => route('user.deposit.return', [
                        'deposit_ref' => $deposit->deposit_ref,
                        'return_token' => hash_hmac('sha256', $deposit->deposit_ref, (string) config('app.key')),
                    ]),
                    'udf1' => (string) $deposit->user_id,
                ],
            );
        } catch (ConnectionException $exception) {
            Log::warning('UPIGateway order request connection failed.', ['deposit_ref' => $deposit->deposit_ref]);
            throw new RuntimeException('Could not connect to UPIGateway. Please try again.', previous: $exception);
        }

        $responseData = $response->json('data') ?? $response->json('result') ?? [];
        $paymentUrl = $responseData['payment_url'] ?? $responseData['paymentUrl'] ?? null;
        if (! $response->successful() || ! is_string($paymentUrl) || ! filter_var($paymentUrl, FILTER_VALIDATE_URL)
            || parse_url($paymentUrl, PHP_URL_SCHEME) !== 'https') {
            Log::warning('UPIGateway rejected order creation.', [
                'deposit_ref' => $deposit->deposit_ref,
                'http_status' => $response->status(),
                'provider_message' => mb_substr((string) ($response->json('msg') ?? $response->json('message') ?? 'No provider message.'), 0, 250),
            ]);

            throw new RuntimeException('UPIGateway could not create a payment checkout.');
        }

        $deposit->forceFill([
            'gateway_reference' => $deposit->deposit_ref,
            'payment_metadata' => [
                'checkout_url' => $paymentUrl,
                'qr_url' => $responseData['qr_url'] ?? $responseData['qrUrl'] ?? $responseData['qr_image'] ?? null,
                'upi_intent' => $responseData['upi_intent'] ?? null,
            ],
        ])->save();

        return route('user.deposit.upi.payment', ['deposit' => $deposit->id]);
    }

    public function verifyAndCredit(string $depositRef): bool
    {
        $deposit = Deposit::query()
            ->where('deposit_ref', $depositRef)
            ->where('payment_provider', 'upigateway')
            ->first();
        if (! $deposit || $deposit->status === 'rejected') {
            return false;
        }
        if ($deposit->status === 'approved') {
            return true;
        }

        $gatewayConfig = $this->gatewayConfig($deposit->provider_mode);
        $token = (string) ($gatewayConfig['key'] ?? '');
        $baseUrl = (string) ($gatewayConfig['base_url'] ?? '');
        $caBundle = (string) config('gateway.upigateway.ca_bundle', '');
        if ($token === '' || $baseUrl === '') {
            return false;
        }

        try {
            $response = Http::asJson()->acceptJson()->withOptions(['verify' => $caBundle !== '' ? $caBundle : true])->connectTimeout(5)->timeout(15)->post(
                $this->endpoint('check_order_status', $baseUrl),
                [
                    'key' => $token,
                    'client_txn_id' => $deposit->deposit_ref,
                    'txn_date' => $deposit->created_at->format('d-m-Y'),
                ],
            );
        } catch (ConnectionException $exception) {
            Log::warning('UPIGateway status check connection failed.', ['deposit_ref' => $depositRef]);

            return false;
        }

        $result = $response->json('data') ?? $response->json('result') ?? [];
        if (! $response->successful()) {
            Log::warning('UPIGateway status check returned an unsuccessful HTTP response.', [
                'deposit_ref' => $depositRef,
                'http_status' => $response->status(),
            ]);

            return false;
        }

        if (! is_array($result) || $result === []) {
            Log::warning('UPIGateway status check returned an unexpected response.', [
                'deposit_ref' => $depositRef,
                'http_status' => $response->status(),
            ]);

            return false;
        }

        $status = strtoupper((string) ($result['status'] ?? $result['txn_status'] ?? $result['txnStatus'] ?? ''));
        if (in_array($status, ['FAILURE', 'FAILED', 'CANCELLED', 'EXPIRED'], true)) {
            DB::transaction(function () use ($deposit): void {
                $lockedDeposit = Deposit::query()->lockForUpdate()->find($deposit->id);
                if ($lockedDeposit?->status === 'pending') {
                    $lockedDeposit->forceFill([
                        'status' => 'rejected',
                        'admin_remark' => 'UPIGateway reported an unsuccessful payment.',
                    ])->save();
                }
            });

            return false;
        }
        if (! in_array($status, ['SUCCESS', 'COMPLETED'], true)) {
            Log::info('UPIGateway payment is not final yet.', [
                'deposit_ref' => $depositRef,
                'provider_status' => $status !== '' ? $status : 'unknown',
            ]);

            return false;
        }

        $paidAmount = (float) ($result['amount'] ?? $result['txn_amount'] ?? 0);
        if (abs($paidAmount - (float) $deposit->amount_inr) > 0.009) {
            Log::warning('UPIGateway deposit amount mismatch.', ['deposit_ref' => $depositRef]);

            return false;
        }

        $utr = (string) ($result['upi_txn_id'] ?? $result['utr'] ?? $result['bank_ref_num'] ?? '');

        return DB::transaction(function () use ($deposit, $utr): bool {
            $lockedDeposit = Deposit::query()->lockForUpdate()->find($deposit->id);
            if (! $lockedDeposit || $lockedDeposit->status !== 'pending') {
                return $lockedDeposit?->status === 'approved';
            }

            $user = $lockedDeposit->user()->lockForUpdate()->firstOrFail();
            $user->deposit_wallet = (float) $user->deposit_wallet + (float) $lockedDeposit->final_amount;
            $user->save();

            $lockedDeposit->forceFill([
                'status' => 'approved',
                'payment_id' => $utr !== '' ? $utr : null,
                'trx_hash' => $utr !== '' ? $utr : null,
                'admin_remark' => 'UPIGateway payment verified and credited.',
            ])->save();

            Transaction::create([
                'user_id' => $user->id,
                'amount' => $lockedDeposit->final_amount,
                'wallet_type' => 'deposit_wallet',
                'type' => 'deposit',
                'trx_type' => '+',
                'charge' => 0,
                'post_balance' => $user->deposit_wallet,
                'description' => "UPIGateway deposit (INR ₹{$lockedDeposit->amount_inr}, Ref: {$lockedDeposit->deposit_ref})",
                'trx_id' => $lockedDeposit->deposit_ref,
            ]);

            return true;
        });
    }

    private function endpoint(string $path, string $baseUrl): string
    {
        $baseUrl = rtrim($baseUrl, '/');
        if (! str_ends_with(parse_url($baseUrl, PHP_URL_PATH) ?: '', '/api')) {
            $baseUrl .= '/api';
        }

        return $baseUrl.'/'.ltrim($path, '/');
    }

    public function isConfigured(): bool
    {
        $gatewayConfig = $this->gatewayConfig();

        return filled($gatewayConfig['key'] ?? null)
            && parse_url((string) ($gatewayConfig['base_url'] ?? ''), PHP_URL_SCHEME) === 'https';
    }

    /**
     * @return array<string, mixed>
     */
    private function gatewayConfig(?string $mode = null): array
    {
        $mode ??= (string) config('gateway.upigateway.mode', 'test');
        if (! in_array($mode, ['test', 'live'], true)) {
            return [];
        }

        return (array) config("gateway.upigateway.modes.{$mode}", []);
    }
}
