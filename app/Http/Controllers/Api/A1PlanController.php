<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\A1PlanCatalog;
use App\Services\A1PlanService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class A1PlanController extends Controller
{
    use ApiResponse;

    public function dthBasicInfo(Request $request, A1PlanService $a1PlanService): JsonResponse
    {
        $validated = $request->validate([
            'mobileno' => ['required', 'string', 'min:5', 'max:30'],
            'operatorcode' => ['required', 'string', 'in:ATV,TTV,VTV,STV,DTV'],
        ]);

        return $this->proxy(fn (): array => $a1PlanService->dthBasicInfo($validated['mobileno'], $validated['operatorcode']), null, 'dthBasicInfo');
    }

    public function dthInfoCheck(Request $request, A1PlanService $a1PlanService): JsonResponse
    {
        $validated = $request->validate([
            'mobileno' => ['required', 'string', 'min:5', 'max:30'],
            'operatorcode' => ['required', 'string', 'in:ATV,TTV,VTV,STV,DTV'],
        ]);

        return $this->proxy(fn (): array => $a1PlanService->dthInfoCheck($validated['mobileno'], $validated['operatorcode']), null, 'dthInfoCheck');
    }

    public function dthInfoWithLastRecharge(Request $request, A1PlanService $a1PlanService): JsonResponse
    {
        $validated = $request->validate([
            'mobileno' => ['required', 'string', 'min:5', 'max:30'],
            'operatorcode' => ['required', 'string', 'in:ATV,TTV,VTV,STV,DTV'],
        ]);

        return $this->proxy(fn (): array => $a1PlanService->dthInfoWithLastRecharge($validated['mobileno'], $validated['operatorcode']), null, 'dthInfoWithLastRecharge');
    }

    public function dthOperatorCheck(Request $request, A1PlanService $a1PlanService): JsonResponse
    {
        $validated = $request->validate([
            'dthnumber' => ['required', 'digits_between:5,20'],
        ]);

        return $this->proxy(fn (): array => $a1PlanService->dthOperatorCheck($validated['dthnumber']), '1', 'dthOperatorCheck');
    }

    public function electricityBillFetch(Request $request, A1PlanService $a1PlanService): JsonResponse
    {
        $validated = $request->validate([
            'operatorcode' => ['required', 'string', 'max:30'],
            'billnumber' => ['required', 'string', 'min:1', 'max:100'],
            'optional1' => ['nullable', 'string', 'max:100'],
            'optional2' => ['nullable', 'string', 'max:100'],
            'optional3' => ['nullable', 'string', 'max:100'],
        ]);
        $validated['operatorcode'] = strtoupper(trim($validated['operatorcode']));

        if (! $this->isKnownOperator($validated['operatorcode'], 'electricity')) {
            return $this->unknownOperatorCodeResponse($validated['operatorcode'], 'electricity', 'electricity board');
        }

        $optionalParameters = array_filter([
            'optional1' => $validated['optional1'] ?? null,
            'optional2' => $validated['optional2'] ?? null,
            'optional3' => $validated['optional3'] ?? null,
        ], fn (?string $value): bool => $value !== null);

        return $this->proxy(fn (): array => $a1PlanService->electricityBillFetch($validated['operatorcode'], $validated['billnumber'], $optionalParameters), '1', 'electricityBillFetch');
    }

    public function postpaidBillFetch(Request $request, A1PlanService $a1PlanService): JsonResponse
    {
        $validated = $request->validate([
            'operatorcode' => ['required', 'string', 'max:30'],
            'mobileno' => ['required', 'string', 'min:5', 'max:30'],
        ]);
        $validated['operatorcode'] = strtoupper(trim($validated['operatorcode']));

        if (! $this->isKnownOperator($validated['operatorcode'], 'postpaid')) {
            return $this->unknownOperatorCodeResponse($validated['operatorcode'], 'postpaid', 'postpaid operator');
        }

        return $this->proxy(fn (): array => $a1PlanService->postpaidBillFetch($validated['operatorcode'], $validated['mobileno']), '1', 'postpaidBillFetch');
    }

    public function fastagBillFetch(Request $request, A1PlanService $a1PlanService): JsonResponse
    {
        $validated = $request->validate([
            'operatorcode' => ['required', 'string', 'max:30'],
            'vehicleno' => ['required', 'string', 'min:4', 'max:20'],
        ]);
        $validated['operatorcode'] = strtoupper(trim($validated['operatorcode']));

        if (! $this->isKnownOperator($validated['operatorcode'], 'fastag')) {
            return $this->unknownOperatorCodeResponse($validated['operatorcode'], 'fastag', 'FASTag issuer');
        }

        return $this->proxy(fn (): array => $a1PlanService->fastagBillFetch($validated['operatorcode'], $validated['vehicleno']), '1', 'fastagBillFetch');
    }

    public function operatorCircleFetch(Request $request, A1PlanService $a1PlanService): JsonResponse
    {
        $validated = $request->validate([
            'mobileno' => ['required', 'digits:10'],
        ]);

        return $this->proxy(
            fn (): array => $a1PlanService->operatorCircleFetch($validated['mobileno']),
            '1',
            'operatorCircleFetch'
        );
    }

    public function planFetch(Request $request, A1PlanService $a1PlanService): JsonResponse
    {
        $validated = $request->validate([
            'operatorcode' => ['required', 'string', 'in:A,RC,V,I,BT,BR'],
            'circle' => ['required', 'string', 'max:10'],
        ]);

        return $this->proxy(
            fn (): array => $a1PlanService->planFetch($validated['operatorcode'], $validated['circle']),
            '0',
            'planFetch'
        );
    }

    public function rofferCheck(Request $request, A1PlanService $a1PlanService): JsonResponse
    {
        $validated = $request->validate([
            'operatorcode' => ['required', 'string', 'in:A,RC,V,I,BT,BR'],
            'mobileno' => ['required', 'digits:10'],
        ]);

        return $this->proxy(
            fn (): array => $a1PlanService->rofferCheck($validated['operatorcode'], $validated['mobileno']),
            '1',
            'rofferCheck'
        );
    }

    /** @param callable(): array<string, mixed> $fetch */
    private function proxy(callable $fetch, ?string $expectedStatus, string $endpoint): JsonResponse
    {
        try {
            $providerResponse = $fetch();
        } catch (Throwable $exception) {
            Log::warning('A1 Plan API proxy request failed', [
                'endpoint' => $endpoint,
                'exception' => $exception::class,
            ]);

            return $this->errorResponse($exception->getMessage(), 503);
        }

        $providerError = (string) ($providerResponse['ERROR'] ?? $providerResponse['error'] ?? '');
        $providerStatus = (string) ($providerResponse['STATUS'] ?? $providerResponse['status'] ?? '');
        if ($providerError === '0' && ($expectedStatus === null || $providerStatus === '' || $providerStatus === $expectedStatus)) {
            return $this->successResponse($providerResponse, 'Live A1 Plan API response fetched successfully.');
        }

        $message = (string) ($providerResponse['Message'] ?? $providerResponse['MESSAGE'] ?? 'A1 Plan API rejected the request.');

        return $this->errorResponse(mb_substr($message, 0, 200), 422, [
            'provider_response' => $providerResponse,
        ]);
    }

    private function isKnownOperator(string $operatorCode, string $category): bool
    {
        return A1PlanCatalog::query()
            ->where('catalog_type', 'operator')
            ->where('category', $category)
            ->where('operator_code', $operatorCode)
            ->exists();
    }

    private function unknownOperatorCodeResponse(string $operatorCode, string $category, string $label): JsonResponse
    {
        $suggestions = A1PlanCatalog::query()
            ->where('catalog_type', 'operator')
            ->where('category', $category)
            ->where('operator_code', 'like', $operatorCode.'%')
            ->orderBy('operator_code')
            ->limit(5)
            ->pluck('operator_code')
            ->all();

        $message = $suggestions === []
            ? "`{$operatorCode}` is not listed as an A1Plan {$label} code."
            : "`{$operatorCode}` is not an exact A1Plan {$label} code. Use one of the matching codes: ".implode(', ', $suggestions).'.';

        return $this->validationErrorResponse([
            'operatorcode' => [$message],
            'suggested_operator_codes' => $suggestions,
        ]);
    }
}
