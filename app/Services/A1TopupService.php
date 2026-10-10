<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
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
        $this->username = (string) config('a1topup.username', '');
        $this->password = (string) config('a1topup.password', '');
    }

    /**
     * Submit a Recharge request to A1Topup Gateway.
     * Endpoint: GET/POST https://business.a1topup.com/recharge/api
     */
    public function doRecharge(string $orderId, string $operatorCode, string $number, float $amount, ?string $circleCode = null, ?string $value1 = null, ?string $value2 = null): array
    {
        if (! $this->hasCredentials()) {
            return [
                'status' => 'Unknown',
                'message' => 'A1Topup API credentials are not configured on this server.',
            ];
        }

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
            $client = $this->httpClient(30);
            $response = $client->get($url, $queryParams);

            if (! $response->successful()) {
                Log::error('A1Topup Recharge API HTTP Error', [
                    'orderid' => $orderId,
                    'status_code' => $response->status(),
                    'body' => $response->body(),
                ]);

                return [
                    'status' => 'Unknown',
                    'message' => $this->safeResponseMessage($response->body(), $response->header('Content-Type')),
                    'http_status' => $response->status(),
                    'raw' => $response->body(),
                ];
            }

            return $this->parseResponse($response->body(), $response->header('Content-Type'));
        } catch (\Throwable $e) {
            Log::error('A1Topup Recharge API Exception', [
                'orderid' => $orderId,
                'error' => $this->sanitizeDiagnostic($e->getMessage()),
            ]);

            return [
                'status' => 'Unknown',
                'message' => $this->sanitizeDiagnostic($e->getMessage()),
            ];
        }
    }

    /**
     * Fetch Live Provider Account Wallet Balance from A1Topup.
     * Endpoint: GET/POST https://business.a1topup.com/recharge/balance
     */
    public function checkBalance(): array
    {
        if (! $this->hasCredentials()) {
            return [
                'status' => 'error',
                'balance' => '0.00',
                'message' => 'A1Topup API credentials are not configured on this server.',
            ];
        }

        $url = "{$this->baseUrl}/recharge/balance";

        $queryParams = [
            'username' => $this->username,
            'pwd' => $this->password,
            'format' => 'json',
        ];

        try {
            $client = $this->httpClient(15);
            $response = $client->get($url, $queryParams);

            if ($response->successful()) {
                return array_merge(
                    $this->parseResponse($response->body(), $response->header('Content-Type')),
                    [
                        'http_status' => $response->status(),
                        'content_type' => $response->header('Content-Type'),
                    ]
                );
            }

            return [
                'status' => 'error',
                'balance' => '0.00',
                'message' => 'Failed to fetch provider balance.',
            ];
        } catch (\Throwable $e) {
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
        if (! $this->hasCredentials()) {
            return [
                'status' => 'Unknown',
                'message' => 'A1Topup API credentials are not configured on this server.',
            ];
        }

        $url = "{$this->baseUrl}/recharge/status";

        $queryParams = [
            'username' => $this->username,
            'pwd' => $this->password,
            'orderid' => $orderId,
            'format' => 'json',
        ];

        try {
            $client = $this->httpClient(20);
            $response = $client->get($url, $queryParams);

            if ($response->successful()) {
                return array_merge(
                    $this->parseResponse($response->body(), $response->header('Content-Type')),
                    [
                        'http_status' => $response->status(),
                        'content_type' => $response->header('Content-Type'),
                    ]
                );
            }

            return [
                'status' => 'Unknown',
                'message' => $this->safeResponseMessage($response->body(), $response->header('Content-Type')),
                'http_status' => $response->status(),
            ];
        } catch (\Throwable $e) {
            return [
                'status' => 'Error',
                'message' => $this->sanitizeDiagnostic($e->getMessage()),
            ];
        }
    }

    /**
     * Parse A1Topup's documented JSON, CSV, and XML response formats.
     *
     * @return array<string, mixed>
     */
    protected function parseResponse(string $body, ?string $contentType = null): array
    {
        $trimmedBody = trim($body);
        $json = json_decode($trimmedBody, true);

        if (is_array($json)) {
            if (! isset($json['status']) && isset($json['Status'])) {
                $json['status'] = $json['Status'];
            }

            if (! isset($json['message'])) {
                foreach (['msg', 'error', 'remarks', 'description'] as $messageKey) {
                    if (isset($json[$messageKey]) && is_scalar($json[$messageKey])) {
                        $json['message'] = $this->safeResponseMessage((string) $json[$messageKey]);
                        break;
                    }
                }
            } elseif (is_scalar($json['message'])) {
                $json['message'] = $this->safeResponseMessage((string) $json['message']);
            }

            return $json;
        }

        if (preg_match('/^(success|approved|failure|failed|pending|processing|unknown|error)$/i', $trimmedBody)) {
            return [
                'status' => ucfirst(strtolower($trimmedBody)),
                'raw' => $trimmedBody,
            ];
        }

        if (preg_match('/authentication\s+fail(?:ed)?/i', $trimmedBody)) {
            return [
                'status' => 'Authentication Failed',
                'message' => 'A1Topup rejected the API credentials or API access for this account. Confirm the issued API username/password with A1Topup.',
                'raw' => $trimmedBody,
            ];
        }

        if (str_contains(strtolower((string) $contentType), 'xml') || str_starts_with($trimmedBody, '<')) {
            if (preg_match_all('/<([a-zA-Z0-9_]+)>(.*?)<\/\\1>/s', $trimmedBody, $xmlFields, PREG_SET_ORDER)) {
                $parsedXml = [];
                foreach ($xmlFields as $field) {
                    $parsedXml[$field[1]] = html_entity_decode(strip_tags($field[2]), ENT_QUOTES | ENT_XML1, 'UTF-8');
                }

                if ($parsedXml !== []) {
                    return $parsedXml;
                }
            }
        }

        $fields = str_getcsv($trimmedBody);

        if (count($fields) >= 6) {
            return [
                'txid' => trim((string) $fields[0]),
                'status' => trim((string) $fields[1]),
                'opid' => trim((string) $fields[2]),
                'number' => trim((string) $fields[3]),
                'amount' => trim((string) $fields[4]),
                'orderid' => trim((string) $fields[5]),
                'raw' => $trimmedBody,
            ];
        }

        Log::warning('A1Topup response could not be parsed', [
            'content_type' => $contentType,
            'body' => $trimmedBody,
        ]);

        return [
            'status' => 'Unknown',
            'message' => $this->safeResponseMessage($trimmedBody, $contentType),
            'raw' => $trimmedBody,
        ];
    }

    protected function httpClient(int $timeout): PendingRequest
    {
        $caBundle = config('a1topup.ca_bundle');
        $verify = $caBundle ?: (bool) config('a1topup.verify_ssl', true);

        return Http::connectTimeout(5)
            ->timeout($timeout)
            ->withOptions(['verify' => $verify]);
    }

    protected function sanitizeDiagnostic(string $message): string
    {
        return preg_replace('/(username|pwd|password)=([^&\s]+)/i', '$1=[redacted]', $message) ?? $message;
    }

    protected function hasCredentials(): bool
    {
        return $this->username !== '' && $this->password !== '';
    }

    protected function safeResponseMessage(string $body, ?string $contentType = null): string
    {
        $trimmedBody = trim($body);

        if ($trimmedBody === '') {
            return 'A1Topup returned an empty response.';
        }

        if (str_contains(strtolower((string) $contentType), 'html') || preg_match('/^<!doctype html|^<html\b/i', $trimmedBody)) {
            $pageText = preg_replace('/<(script|style)\b[^>]*>.*?<\/\1>/is', ' ', $trimmedBody) ?? $trimmedBody;
            $pageText = html_entity_decode(strip_tags($pageText), ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $pageText = preg_replace('/\s+/', ' ', $pageText) ?? $pageText;
            $pageText = $this->sanitizeDiagnostic($pageText);
            $pageText = preg_replace('/\b\d{10,}\b/', '[redacted number]', $pageText) ?? $pageText;

            if (preg_match('/<title[^>]*>(.*?)<\/title>/is', $trimmedBody, $titleMatch)) {
                $title = $this->sanitizeDiagnostic(strip_tags($titleMatch[1]));
                $title = mb_substr(trim($title), 0, 100);

                return 'A1Topup returned an HTML page instead of an API response. Page title: '.$title.'. Page text: '.mb_substr($pageText, 0, 180);
            }

            return 'A1Topup returned an HTML page instead of an API response. Page text: '.mb_substr($pageText, 0, 180);
        }

        $message = html_entity_decode(strip_tags($trimmedBody), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $message = preg_replace('/\s+/', ' ', $message) ?? $message;
        $message = $this->sanitizeDiagnostic($message);
        $message = preg_replace('/\b\d{10,}\b/', '[redacted number]', $message) ?? $message;

        return mb_substr($message, 0, 300);
    }
}
