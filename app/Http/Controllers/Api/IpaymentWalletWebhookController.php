<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Services\IpaymentWalletService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IpaymentWalletWebhookController extends Controller
{
    public function __invoke(Request $request, IpaymentWalletService $ipaymentWallet): JsonResponse
    {
        $depositRef = (string) ($request->input('txnId') ?? $request->input('transaction_id') ?? $request->input('client_txn_id'));
        if ($depositRef === '') {
            return response()->json(['status' => false, 'message' => 'Missing transaction reference.'], 400);
        }

        $deposit = Deposit::query()
            ->where('deposit_ref', $depositRef)
            ->where('payment_provider', 'ipaymentwallet')
            ->first();
        if (! $deposit) {
            return response()->json(['status' => false, 'message' => 'Deposit not found.'], 404);
        }

        $eventStatus = strtolower((string) ($request->input('status') ?? $request->input('event') ?? ''));
        if ($eventStatus !== '' && ! in_array($eventStatus, ['success', 'completed', 'paid', 'payment_confirmed'], true)) {
            return response()->json(['status' => false, 'message' => 'Payment is not successful.'], 202);
        }
        $webhookAmount = $request->input('amount') ?? $request->input('paid_amount');
        if ($webhookAmount !== null && (! is_numeric($webhookAmount) || abs((float) $webhookAmount - (float) $deposit->amount) > 0.009)) {
            return response()->json(['status' => false, 'message' => 'Payment amount mismatch.'], 422);
        }

        $credited = $ipaymentWallet->verifyAndCredit($depositRef);

        return response()->json([
            'status' => $credited,
            'message' => $credited ? 'Payment verified.' : 'Payment is pending provider verification.',
        ], $credited ? 200 : 202);
    }
}
