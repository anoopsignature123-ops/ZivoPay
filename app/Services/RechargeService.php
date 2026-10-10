<?php

namespace App\Services;

use App\Models\Recharge;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class RechargeService
{
    public function __construct(
        protected A1TopupService $a1topupService
    ) {}

    /**
     * Process user recharge request with wallet validation, deduction, gateway hit, and auto-refund.
     */
    public function processRecharge(User $user, array $data): Recharge
    {
        $amount = (float) $data['amount'];
        $operatorCode = trim((string) $data['operator_code']);
        $circleCode = ! empty($data['circle_code']) ? trim((string) $data['circle_code']) : null;
        $number = trim((string) $data['number']);
        $value1 = ! empty($data['value1']) ? trim((string) $data['value1']) : null;
        $value2 = ! empty($data['value2']) ? trim((string) $data['value2']) : null;
        $serviceType = ! empty($data['service_type']) ? trim((string) $data['service_type']) : $this->detectServiceType($operatorCode);
        $operatorName = $data['operator_name'] ?? $this->lookupOperatorName($operatorCode);

        // 1. Wallet Balance Pre-Check
        if ((float) $user->deposit_wallet < $amount) {
            throw ValidationException::withMessages([
                'amount' => [
                    'Insufficient Fund Wallet balance (Available: ₹'.number_format((float) $user->deposit_wallet, 2).'). Please add funds to your wallet.',
                ],
            ]);
        }

        $orderId = 'RCG-'.strtoupper(Str::random(10));

        // 2. Deduct Balance & Log Pending Transaction inside DB Transaction
        /** @var Recharge $recharge */
        $recharge = DB::transaction(function () use ($user, $orderId, $serviceType, $operatorCode, $operatorName, $circleCode, $number, $value1, $value2, $amount) {
            // Deduct Fund Wallet balance
            $user->decrement('deposit_wallet', $amount);

            // Log Transaction Entry
            Transaction::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'wallet_type' => 'deposit_wallet',
                'type' => 'recharge',
                'description' => "Recharge payment for {$number} ({$operatorName}) - Ref: {$orderId}",
                'trx_id' => $orderId,
            ]);

            // Create Pending Recharge Entry
            return Recharge::create([
                'user_id' => $user->id,
                'order_id' => $orderId,
                'service_type' => $serviceType,
                'operator_code' => $operatorCode,
                'operator_name' => $operatorName,
                'circle_code' => $circleCode,
                'number' => $number,
                'value1' => $value1,
                'value2' => $value2,
                'amount' => $amount,
                'status' => 'pending',
            ]);
        });

        // 3. Execute Gateway Hit via A1Topup API
        $response = $this->a1topupService->doRecharge(
            orderId: $orderId,
            operatorCode: $operatorCode,
            number: $number,
            amount: $amount,
            circleCode: $circleCode,
            value1: $value1,
            value2: $value2
        );

        $gatewayStatus = strtolower(trim((string) ($response['status'] ?? 'unknown')));

        // 4. Handle Gateway Response & Auto-Refund if Failed
        if (in_array($gatewayStatus, ['success', 'approved'], true)) {
            $recharge->update([
                'status' => 'success',
                'txid' => $response['txid'] ?? null,
                'opid' => $response['opid'] ?? null,
                'api_response' => $response,
            ]);
        } elseif (in_array($gatewayStatus, ['failure', 'failed'], true)) {
            // Perform Instant Auto-Refund
            $this->refundRecharge($recharge, 'Gateway execution failed: '.($response['message'] ?? 'Rejected by operator'), $response);
        } else {
            // Keep pending
            $recharge->update([
                'txid' => $response['txid'] ?? null,
                'opid' => $response['opid'] ?? null,
                'api_response' => $response,
            ]);
        }

        return $recharge->fresh();
    }

    /**
     * Refund a failed or stuck recharge back to User's Fund Wallet.
     */
    public function refundRecharge(Recharge $recharge, string $reason = 'Recharge failed - Fund refunded', ?array $apiResponse = null): bool
    {
        if (in_array($recharge->status, ['refunded', 'success'], true)) {
            return false;
        }

        return DB::transaction(function () use ($recharge, $reason, $apiResponse) {
            $lockedRecharge = Recharge::whereKey($recharge->id)->lockForUpdate()->firstOrFail();

            if (in_array($lockedRecharge->status, ['refunded', 'success'], true)) {
                return false;
            }

            $user = $recharge->user;
            $amount = (float) $recharge->amount;

            // Refund Fund Wallet Balance
            $user->increment('deposit_wallet', $amount);

            // Log Refund Transaction
            Transaction::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'wallet_type' => 'deposit_wallet',
                'type' => 'recharge_refund',
                'description' => "Auto Refund for failed recharge {$recharge->number} (Order: {$recharge->order_id})",
                'trx_id' => 'REF-'.$recharge->order_id,
            ]);

            // Update Recharge status
            $lockedRecharge->update([
                'status' => 'refunded',
                'admin_remark' => $reason,
                'api_response' => $apiResponse ?? $recharge->api_response,
            ]);

            return true;
        });
    }

    /**
     * Handle Webhook Callback from A1Topup Gateway.
     */
    public function handleCallback(array $params): bool
    {
        $orderId = trim((string) ($params['txid'] ?? $params['orderid'] ?? $params['order_id'] ?? ''));

        if (empty($orderId)) {
            return false;
        }

        $recharge = Recharge::where('order_id', $orderId)->first();

        if (! $recharge) {
            return false;
        }

        $status = strtolower(trim((string) ($params['status'] ?? '')));

        if (in_array($status, ['success', 'approved'], true)) {
            if ($recharge->status === 'refunded') {
                return false;
            }

            $recharge->update([
                'status' => 'success',
                'opid' => $params['opid'] ?? $recharge->opid,
                'api_response' => array_merge($recharge->api_response ?? [], $params),
            ]);

            return true;
        }

        if (in_array($status, ['failure', 'failed', 'reversed'], true)) {
            return $this->refundRecharge($recharge, 'Callback received: '.ucfirst($status).' from operator', $params);
        }

        // If status parameter is missing or unknown, sync live status from gateway
        $synced = $this->syncOrderStatus($recharge);

        return in_array(strtolower($synced->status), ['success', 'refunded', 'failed'], true);
    }

    /**
     * Sync live status of a recharge order with A1Topup Gateway.
     */
    public function syncOrderStatus(Recharge $recharge): Recharge
    {
        $response = $this->a1topupService->checkStatus($recharge->order_id);
        $status = strtolower((string) ($response['status'] ?? ''));

        if (in_array($status, ['success'], true)) {
            if ($recharge->status === 'refunded') {
                return $recharge->fresh();
            }

            $recharge->update([
                'status' => 'success',
                'txid' => $response['txid'] ?? $recharge->txid,
                'opid' => $response['opid'] ?? $recharge->opid,
                'api_response' => $response,
            ]);
        } elseif (in_array($status, ['failure', 'failed'], true)) {
            $this->refundRecharge($recharge, 'Live status check: Failed', $response);
        } else {
            $recharge->update([
                'txid' => $response['txid'] ?? $recharge->txid,
                'opid' => $response['opid'] ?? $recharge->opid,
                'api_response' => $response,
            ]);
        }

        return $recharge->fresh();
    }

    /**
     * Fetch paginated user recharge history with filters and summary statistics.
     */
    public function getRechargeHistory(User $user, array $filters = [], int $perPage = 15): array
    {
        $query = Recharge::where('user_id', $user->id);

        if (! empty($filters['search'])) {
            $search = trim((string) $filters['search']);
            $query->where(function ($q) use ($search) {
                $q->where('order_id', 'like', "%{$search}%")
                    ->orWhere('number', 'like', "%{$search}%")
                    ->orWhere('operator_code', 'like', "%{$search}%")
                    ->orWhere('operator_name', 'like', "%{$search}%");
            });
        }

        if (! empty($filters['status']) && in_array($filters['status'], ['pending', 'success', 'failed', 'refunded'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['service_type'])) {
            $query->where('service_type', $filters['service_type']);
        }

        if (! empty($filters['from_date'])) {
            $query->whereDate('created_at', '>=', $filters['from_date']);
        }

        if (! empty($filters['to_date'])) {
            $query->whereDate('created_at', '<=', $filters['to_date']);
        }

        /** @var LengthAwarePaginator $recharges */
        $recharges = $query->orderBy('id', 'desc')->paginate($perPage);

        $stats = [
            'total_spent' => (float) Recharge::where('user_id', $user->id)->where('status', 'success')->sum('amount'),
            'total_success' => Recharge::where('user_id', $user->id)->where('status', 'success')->count(),
            'total_failed' => Recharge::where('user_id', $user->id)->whereIn('status', ['failed', 'refunded'])->count(),
            'total_pending' => Recharge::where('user_id', $user->id)->where('status', 'pending')->count(),
            'current_deposit_wallet_balance' => (float) $user->deposit_wallet,
        ];

        return [
            'recharges' => $recharges,
            'stats' => $stats,
        ];
    }

    /**
     * Helper to detect service type from operator code.
     */
    protected function detectServiceType(string $operatorCode): string
    {
        $dthCodes = ['TTV', 'ATV', 'DTV', 'VTV', 'STV'];
        $fastagCodes = ['AXF', 'ICF', 'SBF', 'PTF', 'HDF', 'KMF', 'APB', 'IFF', 'BBF', 'INDF'];
        $electricityCodes = ['UPPCLU', 'UPPCLR', 'BSES', 'BSESY', 'TPD', 'MSEDC', 'NBE', 'SBE', 'WBSEDCL', 'BESCOM', 'TNEB'];

        if (in_array($operatorCode, $dthCodes, true)) {
            return 'dth';
        }

        if (in_array($operatorCode, $fastagCodes, true)) {
            return 'fastag';
        }

        if (in_array($operatorCode, $electricityCodes, true)) {
            return 'electricity';
        }

        foreach (config('a1topup.operators', []) as $category => $operators) {
            foreach ($operators as $operator) {
                if (strtoupper((string) ($operator['code'] ?? '')) === strtoupper($operatorCode)) {
                    return match ($category) {
                        'mobile', 'dth', 'postpaid', 'electricity', 'gas', 'fastag', 'insurance' => $category,
                        default => 'mobile',
                    };
                }
            }
        }

        return 'mobile';
    }

    /**
     * Helper to lookup human readable operator name from code.
     */
    protected function lookupOperatorName(string $operatorCode): string
    {
        $operators = config('a1topup.operators', []);

        foreach ($operators as $category => $items) {
            foreach ($items as $item) {
                if ($item['code'] === $operatorCode) {
                    return $item['name'];
                }
            }
        }

        return $operatorCode;
    }
}
