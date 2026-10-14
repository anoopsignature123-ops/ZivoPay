<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Models\PaymentGatewaySetting;
use App\Services\AddMoneyConfiguration;
use App\Services\RazorpayService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Throwable;

class PaymentController extends Controller
{
    use ApiResponse;

    public function configuration(AddMoneyConfiguration $addMoneyConfiguration): JsonResponse
    {
        return $this->successResponse(
            $addMoneyConfiguration->forApp(),
            'Add Money configuration retrieved.'
        );
    }

    public function createOrder(Request $request, RazorpayService $razorpay): JsonResponse
    {
        $settings = PaymentGatewaySetting::razorpay();
        if (! $settings->isReady()) {
            return $this->errorResponse('Razorpay is currently unavailable.', 503);
        }
        try {
            $validated = $request->validate([
                'amount' => ['required', 'numeric', 'decimal:0,2', 'min:'.config('gateway.min_deposit', 100), 'max:'.config('gateway.max_deposit', 500000)],
            ], [
                'amount.min' => 'Amount is below the allowed limit.',
                'amount.max' => 'Amount is above the allowed limit.',
            ]);
        } catch (ValidationException $exception) {
            return $this->validationErrorResponse($exception->errors(), 'Please enter a valid Add Money amount.');
        }
        $deposit = null;
        try {
            $deposit = $razorpay->createPendingDeposit((int) $request->user()->id, (float) $validated['amount']);
            $order = $razorpay->createOrder($deposit);
        } catch (Throwable $exception) {
            report($exception);
            if ($deposit && ! $deposit->payment_order_id) {
                try {
                    $deposit->forceFill(['status' => 'rejected', 'admin_remark' => 'Razorpay order creation failed. Please start a new attempt.'])->save();
                } catch (Throwable $cleanupException) {
                    report($cleanupException);
                }
            }

            return $this->errorResponse('Could not start Add Money payment. Please try again.', 502);
        }

        return $this->successResponse([
            'deposit_ref' => $deposit->deposit_ref, 'order_id' => $order['id'],
            'amount' => (int) $order['amount'], 'currency' => $order['currency'],
            'key_id' => $settings->keyId(), 'mode' => $settings->mode,
            'prefill' => [
                'name' => $request->user()->name,
                'email' => $request->user()->email,
                'contact' => $request->user()->mobile,
            ],
        ], 'Add Money payment started.', 201);
    }

    public function verify(Request $request, RazorpayService $razorpay): JsonResponse
    {
        $user = $request->user();
        $validated = $request->validate([
            'deposit_ref' => ['required', 'string', 'max:255'],
            'razorpay_payment_id' => ['required', 'string', 'max:255'],
            'razorpay_order_id' => ['required', 'string', 'max:255'],
            'razorpay_signature' => ['required', 'string', 'max:255'],
        ]);
        $deposit = Deposit::where('deposit_ref', $validated['deposit_ref'])
            ->where('user_id', $user->id)
            ->where('payment_provider', 'razorpay')
            ->firstOrFail();
        if (! hash_equals((string) $deposit->payment_order_id, $validated['razorpay_order_id'])) {
            return $this->errorResponse('Payment order does not match this deposit.', 422);
        }
        if (! $razorpay->hasValidCheckoutSignature(
            $deposit,
            $validated['razorpay_payment_id'],
            $validated['razorpay_signature'],
        )) {
            return $this->errorResponse('Payment verification failed.', 422);
        }
        try {
            $verified = $razorpay->verifyCheckout($deposit, $validated['razorpay_payment_id'], $validated['razorpay_signature']);
        } catch (Throwable $exception) {
            report($exception);
            $verified = false;
        }
        if (! $verified) {
            $deposit->refresh();

            if ($deposit->status === 'pending') {
                return $this->successResponse([
                    'deposit_ref' => $deposit->deposit_ref,
                    'status' => 'pending',
                    'deposit_wallet_balance' => (float) $user->fresh()->deposit_wallet,
                ], 'Payment received. Wallet credit is awaiting Razorpay confirmation.', 202);
            }

            return $this->errorResponse('Payment verification failed.', 422);
        }

        return $this->successResponse([
            'deposit_ref' => $deposit->deposit_ref, 'status' => $deposit->fresh()->status,
            'deposit_wallet_balance' => (float) $user->fresh()->deposit_wallet,
        ], 'Payment verified and Fund Wallet updated.');
    }

    public function status(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'deposit_ref' => ['required', 'string', 'max:255'],
        ]);
        $deposit = Deposit::query()
            ->where('deposit_ref', $validated['deposit_ref'])
            ->where('user_id', $request->user()->id)
            ->where('payment_provider', 'razorpay')
            ->firstOrFail();

        return $this->successResponse([
            'deposit_ref' => $deposit->deposit_ref,
            'status' => $deposit->status,
            'amount' => (float) $deposit->amount,
            'deposit_wallet_balance' => (float) $request->user()->fresh()->deposit_wallet,
        ], 'Add Money payment status retrieved.');
    }

    public function webhook(Request $request, RazorpayService $razorpay): JsonResponse
    {
        $eventId = (string) $request->header('X-Razorpay-Event-Id');
        if ($eventId === '' || ! $razorpay->verifyWebhook(
            $request->getContent(), (string) $request->header('X-Razorpay-Signature'), $eventId
        )) {
            return response()->json(['message' => 'Invalid webhook signature.'], 400);
        }

        return response()->json(['message' => 'Webhook accepted.']);
    }
}
