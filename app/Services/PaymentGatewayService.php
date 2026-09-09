<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PaymentGatewayService
{
    private string $ipaymentwalletUrl = 'https://ipaymentwallet.com/api';

    private string $apiKey;

    public function __construct()
    {
        $this->apiKey = env('PAYMENT_GATEWAY_API_KEY', 'pk_Hwho4MCbvOT8j1e6h254lJOkh647N3Zs');
    }

    /**
     * Create payment request for BEP20 USDT deposit
     */
    public function createPayment(string $txnId, float $amount, ?string $redirectUrl = null, ?string $memberId = null): array
    {
        try {
            $payload = [
                'txnId' => $txnId,
                'amount' => (string) $amount,
                'apiKey' => $this->apiKey,
                'redirectUrl' => $redirectUrl ?? config('app.url'),
            ];

            if ($memberId !== null) {
                $payload['MemberId'] = (string) $memberId;
            }

            $response = Http::timeout(30)->withoutVerifying()->post("{$this->ipaymentwalletUrl}/v1/create-payment", $payload);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            }

            $json = $response->json();
            $errorMessage = $json['message'] ?? $json['error'] ?? $json['msg'] ?? null;
            if (is_array($errorMessage)) {
                $errorMessage = implode(', ', array_map(fn ($val) => is_string($val) ? $val : json_encode($val), $errorMessage));
            }

            return [
                'success' => false,
                'message' => $errorMessage ?: 'Payment gateway initialization failed (HTTP '.$response->status().').',
                'error' => $json,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Payment gateway connection error: '.$e->getMessage(),
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Check payment status
     */
    public function checkPaymentStatus(string $merchantTxnId): array
    {
        try {
            $response = Http::timeout(30)->withoutVerifying()->withHeaders([
                'X-API-Key' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post("{$this->ipaymentwalletUrl}/payment/check-status", [
                'transaction_id' => $merchantTxnId,
            ]);

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'success' => true,
                    'data' => $data['data'] ?? $data,
                    'payment_confirmed' => $data['payment_confirmed'] ?? false,
                ];
            }

            $json = $response->json();
            $errorMessage = $json['message'] ?? $json['error'] ?? $json['msg'] ?? null;
            if (is_array($errorMessage)) {
                $errorMessage = implode(', ', array_map(fn ($val) => is_string($val) ? $val : json_encode($val), $errorMessage));
            }

            return [
                'success' => false,
                'message' => $errorMessage ?: 'Status check failed (HTTP '.$response->status().').',
                'status_code' => $response->status(),
                'response' => $json,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Status check connection error: '.$e->getMessage(),
                'error' => $e->getMessage(),
            ];
        }
    }
}
