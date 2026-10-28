<?php

namespace App\Services;

use App\Models\Deposit;
use App\Models\Transaction;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class IpaymentWalletService
{
    /**
     * @return array<string, mixed>
     */
    public function createPayment(Deposit $deposit): array
    {
        $config = $this->gatewayConfig($deposit->provider_mode);
        $apiKey = (string) ($config['api_key'] ?? '');
        $baseUrl = rtrim((string) ($config['base_url'] ?? ''), '/');
        $caBundle = (string) config('gateway.ipaymentwallet.ca_bundle', '');
        if ($apiKey === '' || ! str_starts_with($baseUrl, 'https://')) {
            throw new RuntimeException('iPaymentWallet is not configured for the selected mode.');
        }

        try {
            $response = Http::acceptJson()->asJson()->withOptions(['verify' => $caBundle !== '' ? $caBundle : true])->connectTimeout(5)->timeout(30)
                ->post($baseUrl.'/v1/create-payment', [
                    'txnId' => $deposit->deposit_ref,
                    'amount' => number_format((float) $deposit->amount, 2, '.', ''),
                    'apiKey' => $apiKey,
                    'redirectUrl' => route('user.deposit.payment', ['deposit' => $deposit->id]),
                    'MemberId' => (string) $deposit->user_id,
                ]);
        } catch (ConnectionException $exception) {
            Log::warning('iPaymentWallet create-payment request failed.', ['deposit_ref' => $deposit->deposit_ref]);
            throw new RuntimeException('Could not connect to iPaymentWallet. Please try again.', previous: $exception);
        }

        $responseData = $response->json();
        if (! $response->successful() || ! is_array($responseData)) {
            Log::warning('iPaymentWallet rejected payment creation.', [
                'deposit_ref' => $deposit->deposit_ref,
                'http_status' => $response->status(),
            ]);
            throw new RuntimeException('iPaymentWallet could not create a payment request.');
        }

        $data = is_array($responseData['data'] ?? null) ? $responseData['data'] : $responseData;
        $gatewayReference = $data['transactionId'] ?? $data['transaction_id'] ?? null;
        $paymentAddress = $data['paymentAddress'] ?? $data['payment_address'] ?? null;
        $network = $data['network'] ?? $data['networkName'] ?? $data['chain'] ?? 'BEP20 (BSC)';
        $qrPageUrl = $data['qrUrl'] ?? $data['qr_url'] ?? null;
        $qrImage = $data['qrImage'] ?? $data['qr_image'] ?? $data['qrImageUrl'] ?? $data['qr_image_url'] ?? null;
        $paymentUrl = $data['paymentUrl'] ?? $data['payment_url'] ?? $data['checkoutUrl'] ?? $data['checkout_url'] ?? null;

        if (! is_string($gatewayReference) || $gatewayReference === '') {
            throw new RuntimeException('iPaymentWallet response is missing the transaction reference.');
        }

        if (! is_string($qrPageUrl) || $qrPageUrl === '') {
            $qrPageUrl = 'https://ipaymentwallet.com/payment/qr/'.rawurlencode($gatewayReference);
        }
        if (! filter_var($qrPageUrl, FILTER_VALIDATE_URL) || parse_url($qrPageUrl, PHP_URL_SCHEME) !== 'https') {
            throw new RuntimeException('iPaymentWallet returned an invalid QR page URL.');
        }
        if (! is_string($qrImage) || $qrImage === '') {
            $qrImage = 'https://ipaymentwallet.com/api/payment/qr/'.rawurlencode($gatewayReference);
        }
        if (! filter_var($qrImage, FILTER_VALIDATE_URL) || parse_url($qrImage, PHP_URL_SCHEME) !== 'https') {
            throw new RuntimeException('iPaymentWallet returned an invalid QR image URL.');
        }
        if (is_string($paymentUrl) && (! filter_var($paymentUrl, FILTER_VALIDATE_URL)
            || parse_url($paymentUrl, PHP_URL_SCHEME) !== 'https')) {
            throw new RuntimeException('iPaymentWallet returned an invalid checkout URL.');
        }
        if (! $paymentAddress && ! $paymentUrl && ! $qrPageUrl) {
            throw new RuntimeException('iPaymentWallet response has no payment instructions.');
        }

        $deposit->forceFill([
            'gateway_reference' => $gatewayReference,
            'payment_order_id' => $gatewayReference,
            'payment_metadata' => [
                'qr_url' => $qrPageUrl,
                'qr_image' => $qrImage,
                'payment_url' => is_string($paymentUrl) ? $paymentUrl : null,
                'payment_address' => is_string($paymentAddress) ? $paymentAddress : null,
                'network' => is_string($network) ? $network : 'BEP20 (BSC)',
            ],
        ])->save();

        return [
            'gateway_reference' => $gatewayReference,
            'payment_address' => is_string($paymentAddress) ? $paymentAddress : null,
            'network' => is_string($network) ? $network : 'BEP20 (BSC)',
            'qr_url' => $qrPageUrl,
            'qr_image' => $qrImage,
            'payment_url' => is_string($paymentUrl) ? $paymentUrl : null,
        ];
    }

    public function verifyAndCredit(string $depositRef): bool
    {
        $deposit = Deposit::query()
            ->where('deposit_ref', $depositRef)
            ->where('payment_provider', 'ipaymentwallet')
            ->first();
        if (! $deposit || $deposit->status === 'rejected') {
            return false;
        }
        if ($deposit->status === 'approved') {
            return true;
        }

        $config = $this->gatewayConfig($deposit->provider_mode);
        $apiKey = (string) ($config['api_key'] ?? '');
        $baseUrl = rtrim((string) ($config['base_url'] ?? ''), '/');
        $caBundle = (string) config('gateway.ipaymentwallet.ca_bundle', '');
        if ($apiKey === '' || $baseUrl === '') {
            return false;
        }

        try {
            $response = Http::acceptJson()->withOptions(['verify' => $caBundle !== '' ? $caBundle : true])->connectTimeout(5)->timeout(20)->withHeaders([
                'X-API-Key' => $apiKey,
                'Content-Type' => 'application/json',
            ])->post($baseUrl.'/payment/check-status', [
                'transaction_id' => $deposit->deposit_ref,
            ]);
        } catch (ConnectionException $exception) {
            Log::warning('iPaymentWallet status check request failed.', ['deposit_ref' => $depositRef]);

            return false;
        }

        $responseData = $response->json();
        if (! $response->successful() || ! is_array($responseData) || ! ($responseData['payment_confirmed'] ?? false)) {
            return false;
        }

        $data = is_array($responseData['data'] ?? null) ? $responseData['data'] : $responseData;
        if (isset($data['payment_confirmed']) && ! (bool) $data['payment_confirmed']) {
            return false;
        }
        if (isset($data['status']) && ! in_array(strtolower((string) $data['status']), ['success', 'completed', 'paid', 'confirmed'], true)) {
            return false;
        }
        $paidAmount = $data['amount'] ?? $data['paid_amount'] ?? $data['received_amount'] ?? null;
        if (! is_numeric($paidAmount) || abs((float) $paidAmount - (float) $deposit->amount) > 0.009) {
            Log::warning('iPaymentWallet deposit amount mismatch.', ['deposit_ref' => $depositRef]);

            return false;
        }

        $paymentId = (string) ($data['transactionId'] ?? $data['transaction_id'] ?? $data['tx_hash'] ?? $data['transaction_hash'] ?? '');

        return DB::transaction(function () use ($deposit, $paymentId): bool {
            $lockedDeposit = Deposit::query()->lockForUpdate()->find($deposit->id);
            if (! $lockedDeposit || $lockedDeposit->status !== 'pending') {
                return $lockedDeposit?->status === 'approved';
            }

            $user = $lockedDeposit->user()->lockForUpdate()->firstOrFail();
            $user->deposit_wallet = (float) $user->deposit_wallet + (float) $lockedDeposit->final_amount;
            $user->save();

            $lockedDeposit->forceFill([
                'status' => 'approved',
                'payment_id' => $paymentId !== '' ? $paymentId : $lockedDeposit->gateway_reference,
                'trx_hash' => $paymentId !== '' ? $paymentId : $lockedDeposit->gateway_reference,
                'admin_remark' => 'iPaymentWallet USD payment verified and credited.',
            ])->save();

            Transaction::create([
                'user_id' => $user->id,
                'amount' => $lockedDeposit->final_amount,
                'wallet_type' => 'deposit_wallet',
                'type' => 'deposit',
                'trx_type' => '+',
                'charge' => 0,
                'post_balance' => $user->deposit_wallet,
                'description' => 'iPaymentWallet USD deposit $'.number_format((float) $lockedDeposit->amount, 2, '.', '')." credited as INR ₹{$lockedDeposit->final_amount} (Ref: {$lockedDeposit->deposit_ref})",
                'trx_id' => $lockedDeposit->deposit_ref,
            ]);

            return true;
        });
    }

    public function isConfigured(): bool
    {
        $mode = (string) config('gateway.ipaymentwallet.mode', 'test');
        $config = $this->gatewayConfig($mode);

        return in_array($mode, ['test', 'live'], true)
            && filled($config['api_key'] ?? null)
            && str_starts_with((string) ($config['base_url'] ?? ''), 'https://');
    }

    /**
     * @return array<string, mixed>
     */
    private function gatewayConfig(?string $mode = null): array
    {
        $mode ??= (string) config('gateway.ipaymentwallet.mode', 'test');
        if (! in_array($mode, ['test', 'live'], true)) {
            return [];
        }

        return (array) config("gateway.ipaymentwallet.modes.{$mode}", []);
    }
}
