<?php

namespace App\Services;

use App\Models\Deposit;
use App\Models\PaymentGatewayEvent;
use App\Models\PaymentGatewaySetting;
use App\Models\Transaction;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;

class RazorpayService
{
    public function createOrder(Deposit $deposit): array
    {
        $settings = PaymentGatewaySetting::razorpay();
        if (! $settings->isReady()) {
            throw new RuntimeException('Razorpay is not configured or enabled.');
        }
        $mode = $settings->mode;
        $request = Http::withBasicAuth($settings->keyIdFor($mode), $settings->keySecretFor($mode))
            ->acceptJson()->connectTimeout(5)->timeout(15)->withOptions($this->sslOptions());
        try {
            $response = $request->post('https://api.razorpay.com/v1/orders', [
                'amount' => (int) round(((float) $deposit->amount) * 100),
                'currency' => 'INR', 'receipt' => $deposit->deposit_ref,
                'notes' => ['deposit_ref' => $deposit->deposit_ref, 'user_id' => (string) $deposit->user_id],
            ]);
        } catch (ConnectionException $exception) {
            Log::warning('Razorpay order request could not connect.', [
                'mode' => $mode,
                'reason' => str_contains($exception->getMessage(), 'cURL error 60') ? 'tls_certificate_validation_failed' : 'connection_failed',
            ]);
            throw new RuntimeException('Unable to connect securely to Razorpay. Please check server SSL and network settings.', previous: $exception);
        }
        if (! $response->successful() || ! filled($response->json('id'))) {
            Log::warning('Razorpay order request was rejected.', [
                'mode' => $mode, 'http_status' => $response->status(), 'error_code' => $response->json('error.code'),
            ]);
            $message = $response->status() === 401
                ? 'Razorpay rejected the active API credentials. Recheck credentials for the selected mode.'
                : 'Razorpay could not create the order. Please check the gateway settings and try again.';
            throw new RuntimeException($message);
        }
        $order = $response->json();
        $deposit->forceFill([
            'payment_order_id' => $order['id'], 'gateway_reference' => $order['id'],
            'payment_metadata' => ['mode' => $mode],
        ])->save();

        return $order;
    }

    public function verifyCheckout(Deposit $deposit, string $paymentId, string $signature): bool
    {
        if ($deposit->status === 'approved') {
            return $deposit->status === 'approved' && hash_equals((string) $deposit->payment_id, $paymentId);
        }
        if (! $deposit->payment_order_id || $deposit->status !== 'pending') {
            return false;
        }
        if (! $this->hasValidCheckoutSignature($deposit, $paymentId, $signature)) {
            return false;
        }

        return $this->fetchAndConfirmPayment($deposit, $paymentId);
    }

    public function hasValidCheckoutSignature(Deposit $deposit, string $paymentId, string $signature): bool
    {
        if (! $deposit->payment_order_id) {
            return false;
        }

        $settings = PaymentGatewaySetting::razorpay();
        $mode = (string) ($deposit->payment_metadata['mode'] ?? $settings->mode);
        $secret = $settings->keySecretFor($mode);
        if (! filled($secret)) {
            return false;
        }

        $expected = hash_hmac('sha256', $deposit->payment_order_id.'|'.$paymentId, $secret);

        return hash_equals($expected, $signature);
    }

    public function fetchAndConfirmPayment(Deposit $deposit, string $paymentId): bool
    {
        $settings = PaymentGatewaySetting::razorpay();
        $mode = (string) ($deposit->payment_metadata['mode'] ?? $settings->mode);
        $response = Http::withBasicAuth($settings->keyIdFor($mode), $settings->keySecretFor($mode))
            ->acceptJson()->connectTimeout(5)->timeout(15)
            ->withOptions($this->sslOptions())
            ->get('https://api.razorpay.com/v1/payments/'.rawurlencode($paymentId));
        if (! $response->successful()) {
            return false;
        }
        $payment = $response->json();
        if (($payment['order_id'] ?? null) !== $deposit->payment_order_id
            || (int) ($payment['amount'] ?? 0) !== (int) round(((float) $deposit->amount) * 100)
            || ($payment['currency'] ?? null) !== 'INR') {
            return false;
        }
        if (($payment['status'] ?? null) === 'authorized') {
            $captureResponse = Http::withBasicAuth($settings->keyIdFor($mode), $settings->keySecretFor($mode))
                ->acceptJson()->connectTimeout(5)->timeout(15)
                ->withOptions($this->sslOptions())
                ->post('https://api.razorpay.com/v1/payments/'.rawurlencode($paymentId).'/capture', [
                    'amount' => (int) round(((float) $deposit->amount) * 100), 'currency' => 'INR',
                ]);
            if ($captureResponse->successful()) {
                $payment = $captureResponse->json();
            } elseif (($captureResponse->json('error.code') ?? null) !== 'BAD_REQUEST_ERROR') {
                return false;
            } else {
                $refreshResponse = Http::withBasicAuth($settings->keyIdFor($mode), $settings->keySecretFor($mode))
                    ->acceptJson()->connectTimeout(5)->timeout(15)
                    ->withOptions($this->sslOptions())
                    ->get('https://api.razorpay.com/v1/payments/'.rawurlencode($paymentId));
                if (! $refreshResponse->successful()) {
                    return false;
                }
                $payment = $refreshResponse->json();
            }
        }
        if (($payment['status'] ?? null) !== 'captured') {
            return false;
        }

        return $this->confirmPayment($deposit, $paymentId);
    }

    public function verifyWebhook(string $body, string $signature, string $eventId): bool
    {
        $settings = PaymentGatewaySetting::razorpay();
        $validSignature = collect(['test', 'live'])->contains(function (string $mode) use ($settings, $body, $signature): bool {
            $secret = $settings->webhookSecretFor($mode);

            return filled($secret) && hash_equals(hash_hmac('sha256', $body, $secret), $signature);
        });
        if (! $validSignature) {
            return false;
        }
        $payload = json_decode($body, true);
        $eventType = (string) ($payload['event'] ?? 'unknown');
        try {
            DB::transaction(function () use ($eventId, $eventType, $body, $payload): void {
                PaymentGatewayEvent::create([
                    'gateway' => 'razorpay', 'event_id' => $eventId, 'event_type' => $eventType,
                    'payload_hash' => hash('sha256', $body), 'processed_at' => now(),
                ]);
                $payment = data_get($payload, 'payload.payment.entity');
                if (in_array($eventType, ['payment.failed', 'order.cancelled'], true)
                    && is_array($payment)
                    && ($payment['status'] ?? null) === 'failed') {
                    $deposit = Deposit::where('payment_order_id', $payment['order_id'] ?? null)->first();
                    if ($deposit && ($payment['currency'] ?? null) === 'INR'
                        && (int) ($payment['amount'] ?? 0) === (int) round(((float) $deposit->amount) * 100)) {
                        $this->failPayment($deposit, (string) ($payment['id'] ?? ''));
                    }
                }
                if ($eventType === 'payment.captured'
                    && is_array($payment) && ($payment['status'] ?? null) === 'captured') {
                    $deposit = Deposit::where('payment_order_id', $payment['order_id'] ?? null)->first();
                    if ($deposit && ($payment['currency'] ?? null) === 'INR'
                        && (int) ($payment['amount'] ?? 0) === (int) round(((float) $deposit->amount) * 100)) {
                        $this->confirmPayment($deposit, (string) ($payment['id'] ?? ''));
                    }
                }
            });
        } catch (UniqueConstraintViolationException) {
            return true;
        }

        return true;
    }

    public function confirmPayment(Deposit $deposit, string $paymentId): bool
    {
        return DB::transaction(function () use ($deposit, $paymentId): bool {
            $lockedDeposit = Deposit::query()->lockForUpdate()->find($deposit->id);
            if (! $lockedDeposit || $lockedDeposit->status === 'approved') {
                return $lockedDeposit?->payment_id === $paymentId;
            }
            if ($lockedDeposit->status !== 'pending'
                || $lockedDeposit->payment_provider !== 'razorpay'
                || ! $lockedDeposit->payment_order_id
                || $paymentId === '') {
                return false;
            }
            $lockedDeposit->forceFill([
                'status' => 'approved', 'payment_id' => $paymentId, 'trx_hash' => $paymentId,
                'admin_remark' => 'Razorpay payment verified and credited.',
            ])->save();
            $user = $lockedDeposit->user()->lockForUpdate()->firstOrFail();
            $user->increment('deposit_wallet', $lockedDeposit->final_amount);
            Transaction::create([
                'user_id' => $user->id, 'amount' => $lockedDeposit->final_amount,
                'wallet_type' => 'deposit_wallet', 'type' => 'deposit',
                'description' => "Fund deposit via Razorpay (Ref: {$lockedDeposit->deposit_ref}, Payment: {$paymentId})",
                'trx_id' => $lockedDeposit->deposit_ref,
            ]);

            return true;
        });
    }

    public function failPayment(Deposit $deposit, string $paymentId): bool
    {
        if ($paymentId === '') {
            return false;
        }

        return DB::transaction(function () use ($deposit, $paymentId): bool {
            $lockedDeposit = Deposit::query()->lockForUpdate()->find($deposit->id);
            if (! $lockedDeposit || $lockedDeposit->status !== 'pending'
                || $lockedDeposit->payment_provider !== 'razorpay'
                || ! $lockedDeposit->payment_order_id) {
                return false;
            }

            $lockedDeposit->forceFill([
                'status' => 'rejected',
                'payment_id' => $paymentId,
                'trx_hash' => $paymentId,
                'admin_remark' => 'Razorpay reported that the payment failed.',
            ])->save();

            return true;
        });
    }

    public function createPendingDeposit(int $userId, float $amount): Deposit
    {
        return Deposit::create([
            'user_id' => $userId, 'deposit_ref' => 'DEP-'.Str::upper(Str::random(12)),
            'amount' => $amount, 'charge' => 0, 'final_amount' => $amount,
            'payment_method' => 'Razorpay', 'payment_provider' => 'razorpay', 'status' => 'pending',
        ]);
    }

    private function sslOptions(): array
    {
        $caBundlePath = trim((string) config('gateway.razorpay_ca_bundle'));
        if ($caBundlePath === '') {
            return [];
        }
        if (! File::isFile($caBundlePath) || ! is_readable($caBundlePath)) {
            throw new RuntimeException('Configured Razorpay CA bundle file is missing or unreadable.');
        }

        return ['verify' => $caBundlePath];
    }
}
