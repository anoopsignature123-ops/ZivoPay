<?php

namespace App\Services\Shop;

use App\Models\PaymentGatewaySetting;
use App\Models\Shop\Order;
use App\Models\Shop\Payment;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class ShopRazorpayService
{
    /** @return array<string, mixed> */
    public function startCheckout(Payment $payment): array
    {
        $settings = PaymentGatewaySetting::razorpay();
        $mode = $settings->mode;
        if (! $settings->isReady()) {
            throw new RuntimeException('Razorpay is not configured or enabled.');
        }

        try {
            $response = Http::withBasicAuth($settings->keyIdFor($mode), $settings->keySecretFor($mode))
                ->acceptJson()->connectTimeout(5)->timeout(15)->withOptions($this->sslOptions())
                ->post('https://api.razorpay.com/v1/orders', [
                    'amount' => (int) round((float) $payment->amount * 100),
                    'currency' => $payment->currency,
                    'receipt' => 'SHOP-'.$payment->order->order_number,
                    'notes' => [
                        'order_number' => $payment->order->order_number,
                        'shop_payment_id' => (string) $payment->id,
                        'user_id' => (string) $payment->user_id,
                    ],
                ]);
        } catch (ConnectionException $exception) {
            Log::warning('Shop Razorpay order request could not connect.', ['mode' => $mode]);
            throw new RuntimeException('Unable to connect to Razorpay. Please try again.', previous: $exception);
        }

        $order = $response->json();
        if (! $response->successful() || ! filled($order['id'] ?? null)) {
            Log::warning('Shop Razorpay order request was rejected.', [
                'mode' => $mode, 'http_status' => $response->status(), 'error_code' => $response->json('error.code'),
            ]);
            throw new RuntimeException('Razorpay could not start this Shop payment.');
        }

        $payment->forceFill([
            'mode' => $mode,
            'payment_order_id' => $order['id'],
            'metadata' => ['checkout_order_id' => $order['id']],
        ])->save();

        return $order;
    }

    public function verifyCheckout(Payment $payment, string $orderId, string $paymentId, string $signature): bool
    {
        if ($payment->provider !== 'razorpay' || $payment->status === 'refunded'
            || ! $payment->payment_order_id || ! hash_equals($payment->payment_order_id, $orderId)) {
            return false;
        }
        if ($payment->status === 'captured') {
            return $payment->payment_id === $paymentId;
        }

        $settings = PaymentGatewaySetting::razorpay();
        $mode = (string) $payment->mode;
        $secret = $settings->keySecretFor($mode);
        if (! filled($secret) || ! hash_equals(hash_hmac('sha256', $orderId.'|'.$paymentId, $secret), $signature)) {
            return false;
        }

        $response = Http::withBasicAuth($settings->keyIdFor($mode), $secret)
            ->acceptJson()->connectTimeout(5)->timeout(15)->withOptions($this->sslOptions())
            ->get('https://api.razorpay.com/v1/payments/'.rawurlencode($paymentId));
        if (! $response->successful()) {
            return false;
        }
        $gatewayPayment = $response->json();
        if (($gatewayPayment['order_id'] ?? null) !== $orderId
            || (int) ($gatewayPayment['amount'] ?? 0) !== (int) round((float) $payment->amount * 100)
            || ($gatewayPayment['currency'] ?? null) !== $payment->currency) {
            return false;
        }

        if (($gatewayPayment['status'] ?? null) === 'authorized') {
            $capture = Http::withBasicAuth($settings->keyIdFor($mode), $secret)
                ->acceptJson()->connectTimeout(5)->timeout(15)->withOptions($this->sslOptions())
                ->post('https://api.razorpay.com/v1/payments/'.rawurlencode($paymentId).'/capture', [
                    'amount' => (int) round((float) $payment->amount * 100), 'currency' => $payment->currency,
                ]);
            if (! $capture->successful()) {
                return false;
            }
            $gatewayPayment = $capture->json();
        }

        if (($gatewayPayment['status'] ?? null) !== 'captured') {
            return false;
        }

        return $this->markCaptured($payment, $paymentId, $gatewayPayment['method'] ?? null);
    }

    public function processWebhook(string $body, string $signature, string $eventId): bool
    {
        $payload = json_decode($body, true);
        if (! is_array($payload) || $eventId === '') {
            return false;
        }
        $event = (string) ($payload['event'] ?? '');
        $gatewayPayment = data_get($payload, 'payload.payment.entity');
        if (! is_array($gatewayPayment) || ! filled($gatewayPayment['order_id'] ?? null)) {
            return false;
        }

        $payment = Payment::query()->where('provider', 'razorpay')
            ->where('payment_order_id', $gatewayPayment['order_id'])->first();
        $settings = PaymentGatewaySetting::razorpay();
        if (! $payment) {
            foreach (['test', 'live'] as $mode) {
                $secret = $settings->webhookSecretFor($mode);
                if (filled($secret) && hash_equals(hash_hmac('sha256', $body, $secret), $signature)) {
                    return true;
                }
            }

            return false;
        }

        $webhookSecret = $settings->webhookSecretFor((string) $payment->mode);
        if (! filled($webhookSecret) || ! hash_equals(hash_hmac('sha256', $body, $webhookSecret), $signature)) {
            return false;
        }

        if (($gatewayPayment['currency'] ?? null) !== $payment->currency
            || (int) ($gatewayPayment['amount'] ?? 0) !== (int) round((float) $payment->amount * 100)) {
            return false;
        }

        try {
            DB::transaction(function () use ($eventId, $event, $body, $gatewayPayment, $payment): void {
                $inserted = DB::table('shop_payment_events')->insertOrIgnore([
                    'event_id' => $eventId,
                    'event_type' => $event,
                    'payload_hash' => hash('sha256', $body),
                    'processed_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                if (! $inserted) {
                    return;
                }

                $lockedPayment = Payment::query()->lockForUpdate()->find($payment->id);
                if (! $lockedPayment || $lockedPayment->status !== 'pending') {
                    return;
                }
                if ($event === 'payment.captured' && ($gatewayPayment['status'] ?? null) === 'captured') {
                    $lockedPayment->update([
                        'status' => 'captured', 'payment_id' => $gatewayPayment['id'],
                        'method' => $gatewayPayment['method'] ?? null, 'paid_at' => now(),
                    ]);
                    Order::query()->whereKey($lockedPayment->order_id)->where('payment_status', 'pending')
                        ->update(['payment_status' => 'paid', 'status' => 'placed']);
                } elseif ($event === 'payment.failed') {
                    $metadata = $lockedPayment->metadata ?? [];
                    $failedAttempts = $metadata['failed_attempts'] ?? [];
                    $failedAttempts[] = [
                        'payment_id' => $gatewayPayment['id'] ?? null,
                        'failed_at' => now()->toIso8601String(),
                    ];
                    $lockedPayment->update(['metadata' => [...$metadata, 'failed_attempts' => $failedAttempts]]);
                }
            }, 3);
        } catch (UniqueConstraintViolationException) {
            return true;
        }

        return true;
    }

    public function refund(Payment $payment): void
    {
        if ($payment->status === 'refunded') {
            return;
        }
        if ($payment->provider !== 'razorpay' || $payment->status !== 'captured' || ! $payment->payment_id) {
            throw new RuntimeException('Only a captured Razorpay Shop payment can be refunded automatically.');
        }

        $settings = PaymentGatewaySetting::razorpay();
        $mode = (string) $payment->mode;
        $response = Http::withBasicAuth($settings->keyIdFor($mode), $settings->keySecretFor($mode))
            ->acceptJson()->connectTimeout(5)->timeout(15)->withOptions($this->sslOptions())
            ->post('https://api.razorpay.com/v1/payments/'.rawurlencode($payment->payment_id).'/refund', [
                'amount' => (int) round((float) $payment->amount * 100),
                'notes' => ['order_number' => $payment->order->order_number, 'purpose' => 'Shop order refund'],
            ]);
        if (! $response->successful() || ! filled($response->json('id'))) {
            throw new RuntimeException('Razorpay could not process the Shop order refund.');
        }

        DB::transaction(function () use ($payment, $response): void {
            $lockedPayment = Payment::query()->lockForUpdate()->findOrFail($payment->id);
            if ($lockedPayment->status === 'refunded') {
                return;
            }
            $lockedPayment->update([
                'status' => 'refunded', 'refund_id' => $response->json('id'), 'refunded_at' => now(),
            ]);
            Order::query()->whereKey($lockedPayment->order_id)->update(['payment_status' => 'refunded']);
        });
    }

    private function markCaptured(Payment $payment, string $paymentId, ?string $method): bool
    {
        if ($paymentId === '') {
            return false;
        }

        return DB::transaction(function () use ($payment, $paymentId, $method): bool {
            $lockedPayment = Payment::query()->lockForUpdate()->find($payment->id);
            if (! $lockedPayment || $lockedPayment->provider !== 'razorpay') {
                return false;
            }
            if ($lockedPayment->status === 'captured') {
                return $lockedPayment->payment_id === $paymentId;
            }
            if ($lockedPayment->status !== 'pending') {
                return false;
            }

            $lockedPayment->update(['status' => 'captured', 'payment_id' => $paymentId, 'method' => $method, 'paid_at' => now()]);
            Order::query()->whereKey($lockedPayment->order_id)->where('payment_status', 'pending')
                ->update(['payment_status' => 'paid', 'status' => 'placed']);

            return true;
        }, 3);
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
