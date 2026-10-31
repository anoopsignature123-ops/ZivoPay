<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\RechargeRequest;
use App\Http\Resources\Api\RechargeResource;
use App\Models\A1PlanCatalog;
use App\Models\Operator;
use App\Models\Recharge;
use App\Services\A1PlanService;
use App\Services\RechargeService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

class RechargeController extends Controller
{
    use ApiResponse;

    /**
     * Get Master Operator List & Telecom Circles List API (100% Dynamic from Database).
     */
    public function operators(Request $request): JsonResponse
    {
        $filter = strtolower((string) ($request->get('type') ?? $request->get('category') ?? $request->get('service_type') ?? ''));
        $search = strtolower((string) $request->get('search', ''));

        $a1PlanCategories = ['mobile', 'dth', 'postpaid', 'electricity', 'fastag'];

        if (in_array($filter, $a1PlanCategories, true)) {
            $dbOperators = A1PlanCatalog::query()
                ->where('catalog_type', 'operator')
                ->where('category', $filter)
                ->when($search !== '', fn ($query) => $query->where('name', 'like', "%{$search}%"))
                ->orderBy('name')
                ->get(['operator_code as code', 'name']);

            return $this->successResponse([
                'category' => $filter,
                'operators' => $dbOperators,
            ], ucfirst($filter).' operators retrieved successfully.');
        }

        $query = Operator::where('is_active', true);

        if (! empty($filter)) {
            $query->where('category', $filter);
        }

        if (! empty($search)) {
            $query->where('name', 'like', "%{$search}%");
        }

        $dbOperators = $query->get();

        if (! empty($filter)) {
            return $this->successResponse([
                'category' => $filter,
                'operators' => $dbOperators->values(),
            ], ucfirst($filter).' operators retrieved successfully.');
        }

        $operators = [
            'mobile' => $dbOperators->where('category', 'mobile')->values(),
            'dth' => $dbOperators->where('category', 'dth')->values(),
            'fastag' => $dbOperators->where('category', 'fastag')->values(),
            'postpaid' => $dbOperators->where('category', 'postpaid')->values(),
            'electricity' => $dbOperators->where('category', 'electricity')->values(),
            'gas' => $dbOperators->where('category', 'gas')->values(),
            'insurance' => $dbOperators->where('category', 'insurance')->values(),
            'voucher' => $dbOperators->whereIn('category', ['voucher', 'google_play'])->values(),
        ];

        foreach ($a1PlanCategories as $category) {
            $operators[$category] = A1PlanCatalog::query()
                ->where('catalog_type', 'operator')
                ->where('category', $category)
                ->when($search !== '', fn ($query) => $query->where('name', 'like', "%{$search}%"))
                ->orderBy('name')
                ->get(['operator_code as code', 'name']);
        }
        $circles = A1PlanCatalog::query()
            ->where('catalog_type', 'circle')
            ->orderByRaw('CAST(circle_code AS UNSIGNED)')
            ->get(['circle_code as code', 'name']);

        return $this->successResponse([
            'operators' => $operators,
            'circles' => $circles,
        ], 'Operator and circle list retrieved successfully.');
    }

    /**
     * Get Master Telecom Circles List API.
     */
    public function circles(): JsonResponse
    {
        $circles = A1PlanCatalog::query()
            ->where('catalog_type', 'circle')
            ->orderByRaw('CAST(circle_code AS UNSIGNED)')
            ->get(['circle_code as code', 'name']);

        return $this->successResponse([
            'circles' => $circles,
        ], 'Telecom circles list retrieved successfully.');
    }

    /**
     * Auto-Detect / Fetch Operator & Telecom Circle by Mobile Number API (Google Pay Style).
     */
    public function fetchOperator(Request $request, A1PlanService $a1PlanService): JsonResponse
    {
        $number = preg_replace('/\D/', '', (string) ($request->get('number') ?? $request->get('mobile')));

        if (strlen($number) < 10) {
            return $this->errorResponse('Please enter a valid 10-digit mobile number.', 422);
        }

        $inputOperatorCode = $request->get('operator_code') ?? $request->get('operator');
        $inputCircleCode = $request->get('circle_code') ?? $request->get('circle');

        if (! empty($inputOperatorCode)) {
            $operatorCode = strtoupper(trim((string) $inputOperatorCode));
            $operatorName = A1PlanCatalog::query()
                ->where('catalog_type', 'operator')
                ->where('category', 'mobile')
                ->where('operator_code', $operatorCode)
                ->value('name');
            if (! $operatorName) {
                return $this->errorResponse('Select a valid A1Plan mobile operator code.', 422);
            }

            $circleCode = $inputCircleCode !== null ? trim((string) $inputCircleCode) : null;
            $circleName = $circleCode !== null
                ? A1PlanCatalog::query()->where('catalog_type', 'circle')->where('circle_code', $circleCode)->value('name')
                : null;
            if ($circleCode !== null && ! $circleName) {
                return $this->errorResponse('Select a valid A1Plan circle code.', 422);
            }

            $detected = [
                'number' => $number,
                'operator_code' => $operatorCode,
                'operator_name' => $operatorName,
                'circle_code' => $circleCode,
                'circle_name' => $circleName,
                'is_explicit_param' => true,
            ];

            return $this->successResponse($detected, 'Operator and circle selected successfully.');
        }

        try {
            $providerResponse = $a1PlanService->operatorCircleFetch($number);
        } catch (\Throwable $exception) {
            return $this->errorResponse($exception->getMessage(), 503);
        }

        if ((string) ($providerResponse['ERROR'] ?? $providerResponse['error'] ?? '') !== '0'
            || (string) ($providerResponse['STATUS'] ?? $providerResponse['status'] ?? '') !== '1') {
            $message = (string) ($providerResponse['Message'] ?? $providerResponse['MESSAGE'] ?? 'A1Plan could not detect this operator and circle.');

            return $this->errorResponse(mb_substr($message, 0, 200), 422, ['provider_response' => $providerResponse]);
        }

        $operatorCode = (string) ($providerResponse['OpCode'] ?? '');
        $circleCode = (string) ($providerResponse['CircleCode'] ?? '');
        $detected = [
            'number' => $number,
            'operator_code' => $operatorCode,
            'operator_name' => (string) ($providerResponse['Operator'] ?? $operatorCode),
            'circle_code' => $circleCode,
            'circle_name' => (string) ($providerResponse['Circle'] ?? $circleCode),
            'is_live_a1plan' => true,
        ];

        return $this->successResponse($detected, 'Operator and circle detected through A1Plan.');
    }

    /**
     * Get Recommended Recharge Plans & DTH Quick Amounts API (100% Dynamic from Database).
     */
    public function plans(Request $request): JsonResponse
    {
        $operatorCode = strtoupper(trim((string) ($request->get('operator_code') ?? $request->get('operatorcode') ?? 'RC')));
        $a1PlanOperator = A1PlanCatalog::query()
            ->where('catalog_type', 'operator')
            ->whereIn('category', ['mobile', 'dth'])
            ->where('operator_code', $operatorCode)
            ->first();

        if ($a1PlanOperator) {
            $circleCode = trim((string) ($request->get('circle_code') ?? $request->get('circle') ?? ''));
            if ($a1PlanOperator->category === 'mobile' && $circleCode === '') {
                return $this->errorResponse('Select a circle to get the correct mobile plans.', 422, [
                    'circle_code' => ['The circle_code parameter is required for mobile plan lookup.'],
                ]);
            }

            if ($a1PlanOperator->category === 'mobile' && ! A1PlanCatalog::query()
                ->where('catalog_type', 'circle')
                ->where('circle_code', $circleCode)
                ->exists()) {
                return $this->errorResponse('The selected A1Plan circle code is invalid.', 422, [
                    'circle_code' => ['Select a circle returned by the recharge circles API.'],
                ]);
            }

            $catalog = A1PlanCatalog::query()
                ->where('catalog_type', $a1PlanOperator->category)
                ->where('operator_code', $operatorCode)
                ->where('circle_code', $a1PlanOperator->category === 'mobile' ? $circleCode : '')
                ->first();

            if (! $catalog) {
                return $this->errorResponse('The live plan catalog has not been synced for this operator and circle yet.', 404);
            }

            return $this->a1PlanCatalogResponse($catalog->payload ?? [], $operatorCode, $circleCode, $a1PlanOperator->category);
        }

        $plans = [];

        // Group plans by category for Google Pay / PhonePe style category tabs
        $categorizedPlans = [];
        foreach ($plans as $p) {
            $cat = $p['category'] ?? 'Recommended Plans';
            if (! isset($categorizedPlans[$cat])) {
                $categorizedPlans[$cat] = [];
            }
            $categorizedPlans[$cat][] = $p;
        }

        $categories = array_keys($categorizedPlans);

        return $this->successResponse([
            'operator_code' => $operatorCode,
            'categories' => $categories,
            'categorized_plans' => $categorizedPlans,
            'recommended_plans' => $categorizedPlans['Recommended Plans'] ?? $plans,
            'all_plans' => $plans,
            'dth_quick_amounts' => [],
        ], 'Recharge plans retrieved successfully.');
    }

    /**
     * A1Plan catalog alias for mobile clients and Postman integrations.
     */
    public function a1PlanMobileCatalog(Request $request): JsonResponse
    {
        return $this->a1PlanCatalog($request, 'mobile');
    }

    /**
     * A1Plan catalog alias for mobile clients and Postman integrations.
     */
    public function a1PlanDthCatalog(Request $request): JsonResponse
    {
        return $this->a1PlanCatalog($request, 'dth');
    }

    /**
     * Submit Mobile / DTH / FASTag / Utility Recharge API.
     */
    public function store(RechargeRequest $request, RechargeService $rechargeService): JsonResponse
    {
        try {
            $user = $request->user();

            $recharge = $rechargeService->processRecharge(
                $user,
                $request->validated()
            );

            $freshUser = $user->fresh();
            $status = strtolower($recharge->status);

            $message = match ($status) {
                'success' => 'Success! Recharge executed successfully.',
                'refunded', 'failed' => 'Recharge failed. The amount has been automatically refunded to your Fund Wallet.',
                default => 'Recharge request submitted and is under processing.',
            };

            return $this->successResponse([
                'recharge' => new RechargeResource($recharge),
                'deposit_wallet_balance' => (float) $freshUser->deposit_wallet,
            ], $message, 201);
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->errors(), 'Recharge request validation failed.');
        } catch (\Exception $e) {
            return $this->errorResponse('Recharge request failed: '.$e->getMessage(), 500);
        }
    }

    /**
     * Get Operators filtered by specific Category.
     */
    public function operatorsByCategory(string $category): JsonResponse
    {
        $cat = strtolower(trim($category));
        if (in_array($cat, ['mobile', 'dth', 'postpaid', 'electricity', 'fastag'], true)) {
            $dbOperators = A1PlanCatalog::query()
                ->where('catalog_type', 'operator')
                ->where('category', $cat)
                ->orderBy('name')
                ->get(['operator_code as code', 'name']);

            return $this->successResponse([
                'category' => $cat,
                'operators' => $dbOperators,
            ], ucfirst($cat).' operators retrieved successfully.');
        }

        $dbOperators = Operator::where('is_active', true)
            ->where('category', $cat)
            ->get();

        return $this->successResponse([
            'category' => $cat,
            'operators' => $dbOperators->values(),
        ], ucfirst($cat).' operators retrieved successfully.');
    }

    /** @param array<string, mixed> $providerResponse */
    private function a1PlanCatalogResponse(array $providerResponse, string $operatorCode, string $circleCode, string $category): JsonResponse
    {
        $plansByCategory = [];
        $providerPlanCategories = $providerResponse['RDATA'] ?? [];
        if (! is_array($providerPlanCategories)) {
            $providerPlanCategories = [];
        }

        foreach ($providerPlanCategories as $planCategory => $providerPlans) {
            if (! is_array($providerPlans)) {
                continue;
            }

            foreach ($providerPlans as $providerPlan) {
                $plan = is_array($providerPlan) ? $providerPlan : ['description' => (string) $providerPlan];
                $plan['amount'] = $plan['rs'] ?? $plan['amount'] ?? null;
                $plan['validity'] = $plan['validity'] ?? null;
                $plan['description'] = $plan['desc'] ?? $plan['description'] ?? '';
                $plan['category'] = (string) $planCategory;
                if ($category === 'mobile') {
                    $plan['circle_code'] = $circleCode;
                }
                $plansByCategory[(string) $planCategory][] = $plan;
            }
        }

        $plans = array_merge(...array_values($plansByCategory ?: [[]]));
        $recommendedCategory = collect(array_keys($plansByCategory))
            ->first(fn (string $name): bool => str_contains(mb_strtolower($name), 'recommended'));

        return $this->successResponse([
            'operator_code' => $operatorCode,
            'circle_code' => $category === 'mobile' ? $circleCode : null,
            'categories' => array_keys($plansByCategory),
            'categorized_plans' => $plansByCategory,
            'recommended_plans' => $recommendedCategory ? $plansByCategory[$recommendedCategory] : $plans,
            'all_plans' => $plans,
            'dth_quick_amounts' => [],
            'synced_at' => A1PlanCatalog::query()
                ->where('catalog_type', $category)
                ->where('operator_code', $operatorCode)
                ->where('circle_code', $category === 'mobile' ? $circleCode : '')
                ->value('synced_at'),
            'provider_response' => $providerResponse,
        ], 'Recharge plans retrieved from the synced A1Plan database catalog.');
    }

    private function a1PlanCatalog(Request $request, string $category): JsonResponse
    {
        $operatorCode = strtoupper(trim((string) ($request->get('operator_code') ?? $request->get('operatorcode') ?? '')));
        if ($operatorCode === '') {
            return $this->errorResponse('The operatorcode parameter is required.', 422, [
                'operatorcode' => ['Select an operator from the recharge operators API.'],
            ]);
        }

        $operatorExists = A1PlanCatalog::query()
            ->where('catalog_type', 'operator')
            ->where('category', $category)
            ->where('operator_code', $operatorCode)
            ->exists();
        if (! $operatorExists) {
            return $this->errorResponse('The selected operator code is not available for this catalog.', 422, [
                'operatorcode' => ['Select an operator from the matching category.'],
            ]);
        }

        $request->merge([
            'operator_code' => $operatorCode,
            'circle_code' => $request->get('circle_code') ?? $request->get('circle'),
        ]);

        return $this->plans($request);
    }

    /**
     * Dedicated Endpoint: Mobile Prepaid Recharge.
     */
    public function mobile(RechargeRequest $request, RechargeService $rechargeService): JsonResponse
    {
        $request->merge(['service_type' => 'mobile']);

        return $this->store($request, $rechargeService);
    }

    /**
     * Dedicated Endpoint: DTH Recharge.
     */
    public function dth(RechargeRequest $request, RechargeService $rechargeService): JsonResponse
    {
        $request->merge(['service_type' => 'dth']);

        return $this->store($request, $rechargeService);
    }

    /**
     * Dedicated Endpoint: Postpaid & Landline Bill Payment.
     */
    public function postpaid(RechargeRequest $request, RechargeService $rechargeService): JsonResponse
    {
        $request->merge(['service_type' => 'postpaid']);

        return $this->store($request, $rechargeService);
    }

    /**
     * Dedicated Endpoint: Electricity Bill Payment.
     */
    public function electricity(RechargeRequest $request, RechargeService $rechargeService): JsonResponse
    {
        $request->merge(['service_type' => 'electricity']);

        return $this->store($request, $rechargeService);
    }

    /**
     * Dedicated Endpoint: Piped Gas & Cylinder Bill Payment.
     */
    public function gas(RechargeRequest $request, RechargeService $rechargeService): JsonResponse
    {
        $request->merge(['service_type' => 'gas']);

        return $this->store($request, $rechargeService);
    }

    /**
     * Dedicated Endpoint: FASTag Recharge.
     */
    public function fastag(RechargeRequest $request, RechargeService $rechargeService): JsonResponse
    {
        $request->merge(['service_type' => 'fastag']);

        return $this->store($request, $rechargeService);
    }

    /**
     * Dedicated Endpoint: Insurance Premium Payment.
     */
    public function insurance(RechargeRequest $request, RechargeService $rechargeService): JsonResponse
    {
        $request->merge(['service_type' => 'insurance']);

        return $this->store($request, $rechargeService);
    }

    /**
     * Dedicated Endpoint: Gift Card & Google Play Voucher Code.
     */
    public function voucher(RechargeRequest $request, RechargeService $rechargeService): JsonResponse
    {
        $request->merge(['service_type' => 'voucher']);

        return $this->store($request, $rechargeService);
    }

    /**
     * Get User Recharge History & Statistics API.
     */
    public function history(Request $request, RechargeService $rechargeService): JsonResponse
    {
        try {
            $user = $request->user();
            $filters = $request->only(['search', 'status', 'service_type', 'from_date', 'to_date']);
            $perPage = (int) $request->get('per_page', 15);

            $data = $rechargeService->getRechargeHistory($user, $filters, $perPage);

            /** @var LengthAwarePaginator $recharges */
            $recharges = $data['recharges'];

            return $this->successResponse([
                'recharges' => RechargeResource::collection($recharges->items()),
                'stats' => $data['stats'],
                'pagination' => [
                    'current_page' => $recharges->currentPage(),
                    'last_page' => $recharges->lastPage(),
                    'per_page' => $recharges->perPage(),
                    'total' => $recharges->total(),
                ],
            ], 'Recharge history retrieved successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to fetch recharge history: '.$e->getMessage(), 500);
        }
    }

    /**
     * Fetch Live Order Status API.
     */
    public function status(Request $request, ?string $orderId = null, ?RechargeService $rechargeService = null): JsonResponse
    {
        try {
            $rechargeService = $rechargeService ?? app(RechargeService::class);
            $targetOrderId = trim((string) ($orderId ?? $request->get('order_id') ?? $request->get('orderid') ?? $request->get('txid')));

            if (empty($targetOrderId)) {
                return $this->errorResponse('Please provide a valid order ID.', 422);
            }

            $user = $request->user();

            $recharge = Recharge::where('user_id', $user->id)->where('order_id', $targetOrderId)->first();

            if (! $recharge) {
                return $this->errorResponse('Recharge order not found.', 404);
            }

            $updatedRecharge = $rechargeService->syncOrderStatus($recharge);

            return $this->successResponse([
                'recharge' => new RechargeResource($updatedRecharge),
            ], 'Order status retrieved.');
        } catch (\Exception $e) {
            return $this->errorResponse('Status query failed: '.$e->getMessage(), 500);
        }
    }

    /**
     * Webhook Callback Endpoint for A1Topup Gateway.
     */
    public function callback(Request $request, RechargeService $rechargeService): JsonResponse
    {
        $processed = $rechargeService->handleCallback($request->all());

        return response()->json([
            'status' => $processed ? 'success' : 'ignored',
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}
