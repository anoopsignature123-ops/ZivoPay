<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class A1TopupService
{
    protected string $baseUrl;

    protected string $username;

    protected string $password;

    public function __construct()
    {
        $this->baseUrl = rtrim((string) config('a1topup.base_url', 'https://business.a1topup.com'), '/');
        $this->username = (string) config('a1topup.username', '500011');
        $this->password = (string) config('a1topup.password', '123');
    }

    /**
     * Submit a Recharge request to A1Topup Gateway.
     * Endpoint: GET/POST https://business.a1topup.com/recharge/api
     */
    public function doRecharge(string $orderId, string $operatorCode, string $number, float $amount, ?string $circleCode = null, ?string $value1 = null, ?string $value2 = null): array
    {
        $url = "{$this->baseUrl}/recharge/api";

        $queryParams = array_filter([
            'username' => $this->username,
            'pwd' => $this->password,
            'operatorcode' => $operatorCode,
            'number' => $number,
            'amount' => $amount,
            'orderid' => $orderId,
            'circlecode' => $circleCode,
            'value1' => $value1,
            'value2' => $value2,
            'format' => 'json',
        ], fn ($val) => $val !== null && $val !== '');

        try {
            $response = Http::timeout(30)->get($url, $queryParams);

            if (! $response->successful()) {
                Log::error('A1Topup Recharge API HTTP Error', [
                    'orderid' => $orderId,
                    'status_code' => $response->status(),
                    'body' => $response->body(),
                ]);

                return [
                    'status' => 'Failure',
                    'message' => 'Gateway HTTP connection error.',
                    'raw' => $response->body(),
                ];
            }

            $data = $response->json();

            if (! is_array($data)) {
                // If CSV or fallback returned
                $body = trim($response->body());
                Log::warning('A1Topup non-JSON response', ['body' => $body]);

                return [
                    'status' => str_contains($body, 'Success') ? 'Success' : 'Failure',
                    'raw' => $body,
                ];
            }

            return $data;
        } catch (\Exception $e) {
            Log::error('A1Topup Recharge API Exception', [
                'orderid' => $orderId,
                'error' => $e->getMessage(),
            ]);

            return [
                'status' => 'Failure',
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Fetch Live Provider Account Wallet Balance from A1Topup.
     * Endpoint: GET/POST https://business.a1topup.com/recharge/balance
     */
    public function checkBalance(): array
    {
        $url = "{$this->baseUrl}/recharge/balance";

        $queryParams = [
            'username' => $this->username,
            'pwd' => $this->password,
            'format' => 'json',
        ];

        try {
            $response = Http::timeout(15)->get($url, $queryParams);

            if ($response->successful() && is_array($response->json())) {
                return $response->json();
            }

            return [
                'status' => 'error',
                'balance' => '0.00',
                'message' => 'Failed to fetch provider balance.',
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'balance' => '0.00',
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Query Live Status of a specific Order ID from A1Topup Gateway.
     * Endpoint: GET/POST https://business.a1topup.com/recharge/status
     */
    public function checkStatus(string $orderId): array
    {
        $url = "{$this->baseUrl}/recharge/status";

        $queryParams = [
            'username' => $this->username,
            'pwd' => $this->password,
            'orderid' => $orderId,
            'format' => 'json',
        ];

        try {
            $response = Http::timeout(20)->get($url, $queryParams);

            if ($response->successful() && is_array($response->json())) {
                return $response->json();
            }

            return [
                'status' => 'Unknown',
                'message' => 'Failed to retrieve transaction status from gateway.',
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'Error',
                'message' => $e->getMessage(),
            ];
        }
    }
}
