<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Services\IpaymentWalletService;
use App\Services\UpigatewayService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Throwable;

class UpigatewayPaymentController extends Controller
{
    use ApiResponse;

    public function configuration(UpigatewayService $upigateway, IpaymentWalletService $ipaymentWallet): JsonResponse
    {
        return $this->successResponse([
            'provider' => 'multi-gateway',
            'enabled' => $upigateway->isConfigured(),
            'currency' => 'INR',
            'wallet_currency' => 'USD',
            'exchange_rate' => (float) config('gateway.usd_to_inr_rate', 100),
            'deposit_methods' => [
                ['currency' => 'INR', 'provider' => 'upigateway', 'enabled' => $upigateway->isConfigured()],
                ['currency' => 'USD', 'provider' => 'ipaymentwallet', 'enabled' => (bool) config('gateway.ipaymentwallet.enabled') && $ipaymentWallet->isConfigured()],
            ],
            'start_url' => route('api.add-fund.start'),
            'status_url' => route('api.add-fund.status'),
            'upigateway_webhook_url' => route('api.upigateway.webhook'),
            'ipayment_webhook_url' => route('api.ipaymentwallet.webhook'),
        ], 'Add Fund configuration retrieved.');
    }

    public function createOrder(Request $request, UpigatewayService $upigateway, IpaymentWalletService $ipaymentWallet): JsonResponse
    {
        $validated = $request->validate([
            'currency' => ['sometimes', 'in:INR,USD'],
            'amount' => ['required', 'numeric', 'decimal:0,2'],
        ]);
        $validated['currency'] ??= 'INR';
        if ($validated['currency'] === 'USD') {
            if (! (bool) config('gateway.ipaymentwallet.enabled') || ! $ipaymentWallet->isConfigured()) {
                return $this->errorResponse('iPaymentWallet is not enabled or configured for the selected mode.', 503);
            }

            return $this->createIpaymentOrder($request, $validated['amount'], $ipaymentWallet);
        }

        if (! $upigateway->isConfigured()) {
            return $this->errorResponse('UPIGateway is currently unavailable.', 503);
        }

        $amountInr = (float) $validated['amount'];
        if ($amountInr < config('gateway.min_deposit_inr', 100) || $amountInr > config('gateway.max_deposit_inr', 500000)) {
            return $this->errorResponse('INR deposit amount is outside the allowed range.', 422);
        }
        $rate = (float) config('gateway.usd_to_inr_rate', 100);
        $amountUsd = round($amountInr / $rate, 2);
        $deposit = Deposit::create([
            'user_id' => $request->user()->id,
            'deposit_ref' => 'DEP-'.Str::upper(Str::random(12)),
            'amount' => $amountUsd,
            'charge' => 0,
            'final_amount' => $amountUsd,
            'amount_inr' => $amountInr,
            'conversion_rate' => $rate,
            'payment_method' => 'UPIGateway UPI (INR)',
            'payment_provider' => 'upigateway',
            'provider_mode' => config('gateway.upigateway.mode', 'test'),
            'status' => 'pending',
        ]);

        try {
            $upigateway->createOrder($deposit->load('user'));
        } catch (Throwable $exception) {
            report($exception);
            $deposit->forceFill(['status' => 'rejected', 'admin_remark' => 'UPIGateway checkout creation failed.'])->save();

            return $this->errorResponse('Could not start UPI payment. Please try again.', 502);
        }

        $expiresAt = now()->addHour();
        $webviewUrl = URL::temporarySignedRoute('app.deposit.upi.webview', $expiresAt, ['deposit' => $deposit->id]);
        $webviewStatusUrl = URL::temporarySignedRoute('app.deposit.upi.status', $expiresAt, ['deposit' => $deposit->id]);

        return $this->successResponse([
            'deposit_id' => $deposit->id,
            'deposit_ref' => $deposit->deposit_ref,
            'status' => $deposit->status,
            'amount_usd' => (float) $deposit->amount,
            'amount_inr' => (float) $deposit->amount_inr,
            'exchange_rate' => $rate,
            'payment_url' => $webviewUrl,
            'webview_url' => $webviewUrl,
            'webview_status_url' => $webviewStatusUrl,
            'api_status_url' => route('api.add-fund.status'),
            'webhook_url' => route('api.upigateway.webhook'),
        ], 'INR UPI payment started.', 201);
    }

    public function status(Request $request, UpigatewayService $upigateway, IpaymentWalletService $ipaymentWallet): JsonResponse
    {
        $validated = $request->validate(['deposit_ref' => ['required', 'string', 'max:255']]);
        $deposit = Deposit::query()->where('deposit_ref', $validated['deposit_ref'])
            ->where('user_id', $request->user()->id)
            ->whereIn('payment_provider', ['upigateway', 'ipaymentwallet'])
            ->firstOrFail();
        if ($deposit->status === 'pending') {
            if ($deposit->payment_provider === 'upigateway') {
                $upigateway->verifyAndCredit($deposit->deposit_ref);
            } else {
                $ipaymentWallet->verifyAndCredit($deposit->deposit_ref);
            }
            $deposit->refresh();
        }

        return $this->successResponse([
            'deposit_ref' => $deposit->deposit_ref,
            'status' => $deposit->status,
            'amount_usd' => (float) $deposit->amount,
            'amount_inr' => (float) $deposit->amount_inr,
        ], 'Payment status retrieved.');
    }

    private function createIpaymentOrder(Request $request, string $amount, IpaymentWalletService $ipaymentWallet): JsonResponse
    {
        if ((float) $amount < 1 || (float) $amount > 50000) {
            return $this->errorResponse('USD deposit amount must be between $1 and $50,000.', 422);
        }

        $amountUsd = (float) $amount;
        $deposit = Deposit::create([
            'user_id' => $request->user()->id,
            'deposit_ref' => 'DEP-'.Str::upper(Str::random(12)),
            'amount' => $amountUsd,
            'charge' => 0,
            'final_amount' => $amountUsd,
            'payment_method' => 'iPaymentWallet USD',
            'payment_provider' => 'ipaymentwallet',
            'provider_mode' => config('gateway.ipaymentwallet.mode', 'test'),
            'status' => 'pending',
        ]);

        try {
            $payment = $ipaymentWallet->createPayment($deposit->load('user'));
        } catch (Throwable $exception) {
            report($exception);
            $deposit->forceFill(['status' => 'rejected', 'admin_remark' => 'iPaymentWallet request creation failed.'])->save();

            return $this->errorResponse('Could not start iPaymentWallet payment. Please try again.', 502);
        }

        return $this->successResponse([
            'deposit_ref' => $deposit->deposit_ref,
            'amount_usd' => (float) $deposit->final_amount,
            ...$payment,
        ], 'USD payment started.', 201);
    }
}
