<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\RechargeRequest;
use App\Http\Resources\Api\RechargeResource;
use App\Models\Operator;
use App\Models\Recharge;
use App\Models\RechargePlan;
use App\Services\OperatorLookupService;
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

        $query = Operator::where('is_active', true);

        if (! empty($filter)) {
            $query->where('category', $filter);
        }

        if (! empty($search)) {
            $query->where('name', 'like', "%{$search}%");
        }

        $dbOperators = $query->get();

        if (! empty($filter)) {
            if ($dbOperators->isEmpty()) {
                $allConfig = config('a1topup.operators', []);
                $dbOperators = collect($allConfig[$filter] ?? []);
            }

            return $this->successResponse([
                'category' => $filter,
                'operators' => $dbOperators->values(),
            ], ucfirst($filter).' operators retrieved successfully.');
        }

        if ($dbOperators->isNotEmpty()) {
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
        } else {
            $operators = config('a1topup.operators', []);
        }

        $circles = config('a1topup.circles', []);

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
        $circles = config('a1topup.circles', []);

        return $this->successResponse([
            'circles' => $circles,
        ], 'Telecom circles list retrieved successfully.');
    }

    /**
     * Auto-Detect / Fetch Operator & Telecom Circle by Mobile Number API (Google Pay Style).
     */
    public function fetchOperator(Request $request): JsonResponse
    {
        $number = preg_replace('/\D/', '', (string) ($request->get('number') ?? $request->get('mobile')));

        if (strlen($number) < 10) {
            return $this->errorResponse('Please enter a valid 10-digit mobile number.', 422);
        }

        $inputOperatorCode = $request->get('operator_code') ?? $request->get('operator');
        $inputCircleCode = $request->get('circle_code') ?? $request->get('circle');

        $detected = OperatorLookupService::detect($number, $inputOperatorCode, $inputCircleCode);

        return $this->successResponse($detected, 'Operator and circle detected successfully.');
    }

    /**
     * Get Recommended Recharge Plans & DTH Quick Amounts API (100% Dynamic from Database).
     */
    public function plans(Request $request): JsonResponse
    {
        $operatorCode = (string) $request->get('operator_code', 'RC');
        $dthQuickAmounts = config('a1topup.dth_quick_amounts', [150, 250, 500, 1000]);

        $dbPlans = RechargePlan::where('operator_code', $operatorCode)
            ->where('is_active', true)
            ->get()
            ->map(function ($plan) {
                return [
                    'id' => $plan->id,
                    'amount' => (float) $plan->amount,
                    'validity' => $plan->validity,
                    'description' => $plan->description,
                    'category' => $plan->category,
                ];
            });

        if ($dbPlans->isNotEmpty()) {
            $plans = $dbPlans->toArray();
        } else {
            $allPlans = config('a1topup.plans', []);

            if (isset($allPlans[$operatorCode])) {
                $plans = $allPlans[$operatorCode];
            } else {
                $operator = Operator::where('code', $operatorCode)->first();
                $category = $operator ? strtolower((string) $operator->category) : null;

                if (! $category) {
                    $configOperators = config('a1topup.operators', []);
                    foreach ($configOperators as $cat => $opList) {
                        foreach ($opList as $op) {
                            if (strtoupper((string) ($op['code'] ?? '')) === strtoupper($operatorCode)) {
                                $category = strtolower((string) $cat);
                                break 2;
                            }
                        }
                    }
                }

                $plans = match ($category) {
                    'fastag' => $allPlans['FASTAG_DEFAULT'] ?? [],
                    'dth' => $allPlans['DTH_DEFAULT'] ?? [],
                    'electricity', 'gas', 'postpaid', 'insurance' => $allPlans['BILL_DEFAULT'] ?? [],
                    default => $allPlans['RC'] ?? [],
                };
            }
        }

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
            'dth_quick_amounts' => $dthQuickAmounts,
        ], 'Recharge plans retrieved successfully.');
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
        $dbOperators = Operator::where('is_active', true)
            ->where('category', $cat)
            ->get();

        if ($dbOperators->isEmpty()) {
            $allConfig = config('a1topup.operators', []);
            $dbOperators = collect($allConfig[$cat] ?? []);
        }

        return $this->successResponse([
            'category' => $cat,
            'operators' => $dbOperators->values(),
        ], ucfirst($cat).' operators retrieved successfully.');
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
