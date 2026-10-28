<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Services\UpigatewayService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UpigatewayWebhookController extends Controller
{
    public function __invoke(Request $request, UpigatewayService $upigateway): JsonResponse
    {
        $depositRef = (string) ($request->input('client_txn_id') ?? $request->input('txnId') ?? $request->input('orderId') ?? $request->input('order_id'));
        if ($depositRef === '') {
            return response()->json(['status' => false, 'message' => 'Missing order reference.'], 400);
        }

        $depositExists = Deposit::query()
            ->where('deposit_ref', $depositRef)
            ->where('payment_provider', 'upigateway')
            ->exists();
        if (! $depositExists) {
            return response()->json(['status' => false, 'message' => 'Deposit not found.'], 404);
        }

        $status = strtolower((string) $request->input('status', ''));
        if (! in_array($status, ['', 'success', 'completed', 'paid'], true)) {
            return response()->json(['status' => false, 'message' => 'Payment is not successful.'], 202);
        }
        $deposit = Deposit::query()->where('deposit_ref', $depositRef)->where('payment_provider', 'upigateway')->firstOrFail();
        $webhookAmount = $request->input('amount');
        if ($webhookAmount !== null && (! is_numeric($webhookAmount) || abs((float) $webhookAmount - (float) $deposit->amount_inr) > 0.009)) {
            return response()->json(['status' => false, 'message' => 'Payment amount mismatch.'], 422);
        }

        $credited = $upigateway->verifyAndCredit($depositRef);

        return response()->json([
            'status' => $credited,
            'message' => $credited ? 'Payment verified.' : 'Payment is pending provider verification.',
        ], $credited ? 200 : 202);
    }
}
