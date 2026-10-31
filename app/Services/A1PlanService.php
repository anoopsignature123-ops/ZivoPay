<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class A1PlanService
{
    /** @return array<string, mixed> */
    public function operatorCircleFetch(string $mobileNumber): array
    {
        return $this->get('operatorCircleFetch', ['mobileno' => $mobileNumber]);
    }

    /** @return array<string, mixed> */
    public function planFetch(string $operatorCode, string $circleCode): array
    {
        return $this->get('planFetch', [
            'operatorcode' => $operatorCode,
            'circle' => $circleCode,
        ]);
    }

    /** @return array<string, mixed> */
    public function dthPlanFetch(string $operatorCode): array
    {
        return $this->get('dthPlanFetch', ['operatorcode' => $operatorCode]);
    }

    /** @return array<string, mixed> */
    public function dthBasicInfo(string $mobileNumber, string $operatorCode): array
    {
        return $this->get('dthBasicInfo', ['mobileno' => $mobileNumber, 'operatorcode' => $operatorCode]);
    }

    /** @return array<string, mixed> */
    public function dthInfoCheck(string $mobileNumber, string $operatorCode): array
    {
        return $this->get('dthInfoCheck', ['mobileno' => $mobileNumber, 'operatorcode' => $operatorCode]);
    }

    /** @return array<string, mixed> */
    public function dthInfoWithLastRecharge(string $mobileNumber, string $operatorCode): array
    {
        return $this->get('dthInfoWithLastRecharge', ['mobileno' => $mobileNumber, 'operatorcode' => $operatorCode]);
    }

    /** @return array<string, mixed> */
    public function dthOperatorCheck(string $dthNumber): array
    {
        return $this->get('dthOperatorCheck', ['dthnumber' => $dthNumber]);
    }

    /** @return array<string, mixed> */
    public function electricityBillFetch(string $operatorCode, string $billNumber, array $optionalParameters): array
    {
        return $this->get('electricityBillFetch', [
            'operatorcode' => $operatorCode,
            'billnumber' => $billNumber,
            ...$optionalParameters,
        ]);
    }

    /** @return array<string, mixed> */
    public function postpaidBillFetch(string $operatorCode, string $mobileNumber): array
    {
        return $this->get('postpaidBillFetch', ['operatorcode' => $operatorCode, 'mobileno' => $mobileNumber]);
    }

    /** @return array<string, mixed> */
    public function fastagBillFetch(string $operatorCode, string $vehicleNumber): array
    {
        return $this->get('fastagBillFetch', ['operatorcode' => $operatorCode, 'vehicleno' => $vehicleNumber]);
    }

    /** @return array<string, mixed> */
    public function rofferCheck(string $operatorCode, string $mobileNumber): array
    {
        return $this->get('rofferCheck', [
            'operatorcode' => $operatorCode,
            'mobileno' => $mobileNumber,
        ]);
    }

    /** @param array<string, string> $parameters
     * @return array<string, mixed>
     */
    private function get(string $endpoint, array $parameters): array
    {
        $username = (string) config('a1plan.username', '');
        $password = (string) config('a1plan.password', '');
        if ($username === '' || $password === '') {
            throw new RuntimeException('A1 Plan API credentials are not configured.');
        }

        try {
            $response = $this->httpClient()->get(
                rtrim((string) config('a1plan.base_url'), '/').'/planapi/'.$endpoint,
                [
                    'username' => $username,
                    'pwd' => $password,
                    ...$parameters,
                ]
            );
        } catch (\Throwable $exception) {
            Log::warning('A1 Plan API connection failed', ['endpoint' => $endpoint]);
            throw new RuntimeException('Could not connect to the A1 Plan API. Check server network and TLS configuration.');
        }

        $payload = $response->json();
        if (! $response->successful() || ! is_array($payload)) {
            Log::warning('A1 Plan API returned an invalid response', [
                'endpoint' => $endpoint,
                'http_status' => $response->status(),
            ]);
            throw new RuntimeException('A1 Plan API is temporarily unavailable.');
        }

        return $payload;
    }

    private function httpClient(): PendingRequest
    {
        $caBundle = (string) config('a1plan.ca_bundle', '');
        if ($caBundle !== '' && ! is_file($caBundle)) {
            throw new RuntimeException('Configured A1 Plan CA bundle file was not found.');
        }

        return Http::acceptJson()
            ->connectTimeout(8)
            ->timeout(25)
            ->withOptions(['verify' => $caBundle !== '' ? $caBundle : true]);
    }
}
