<?php

namespace App\Services\User;

use App\Models\Deposit;
use App\Models\Transaction;
use App\Models\User;
use App\Services\PaymentGatewayService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DepositService
{
    protected PaymentGatewayService $gatewayService;

    public function __construct(PaymentGatewayService $gatewayService)
    {
        $this->gatewayService = $gatewayService;
    }

    /**
     * Create custom fund request & initialize gateway payment
     */
    public function createCustomFund(User $user, float $amount, string $network = 'USDT (BEP20)'): array
    {
        return DB::transaction(function () use ($user, $amount, $network) {
            $depositRef = 'DEP'.strtoupper(Str::random(10));

            $deposit = Deposit::create([
                'user_id' => $user->id,
                'deposit_ref' => $depositRef,
                'amount' => $amount,
                'payment_gateway' => $network,
                'status' => 'pending',
                'wallet_address' => null,
            ]);

            $response = $this->gatewayService->createPayment(
                txnId: $deposit->deposit_ref,
                amount: $amount,
                redirectUrl: route('user.deposits.payment', $deposit->id),
                memberId: ($user->referral_code ?? $user->id).' - ('.$user->email.')',
            );

            if ($response['success']) {
                $apiResponseData = $response['data'] ?? [];
                $innerData = $apiResponseData['data'] ?? $apiResponseData;

                $paymentUrl = $innerData['qrUrl'] ?? $innerData['redirectUrl'] ?? null;

                if ($paymentUrl) {
                    $deposit->update([
                        'wallet_address' => $innerData['paymentAddress'] ?? null,
                        'gateway_reference' => $innerData['transactionId'] ?? null,
                    ]);

                    return [
                        'success' => true,
                        'deposit' => $deposit,
                        'payment_url' => $paymentUrl,
                        'gateway_data' => $innerData,
                    ];
                }
            }

            Log::error('Gateway payment creation failed for deposit: '.$deposit->deposit_ref, $response);

            $failMessage = $response['message'] ?? 'Failed to initialize payment gateway session.';

            return [
                'success' => false,
                'message' => $failMessage,
            ];
        });
    }

    /**
     * Verify and complete deposit
     */
    public function verifyAndProcessDeposit(Deposit $deposit): bool
    {
        if ($deposit->status === 'approved') {
            return true;
        }

        $statusResponse = $this->gatewayService->checkPaymentStatus($deposit->deposit_ref);

        if ($statusResponse['success'] && ($statusResponse['payment_confirmed'] ?? false)) {
            DB::transaction(function () use ($deposit, $statusResponse) {
                $statusData = $statusResponse['data'] ?? [];
                $innerStatusData = $statusData['data'] ?? $statusData;

                $user = User::where('id', $deposit->user_id)->lockForUpdate()->first();

                $txHash = $innerStatusData['tx_hash'] ?? $innerStatusData['transaction_hash'] ?? $deposit->txn_hash;

                $deposit->update([
                    'status' => 'approved',
                    'approved_at' => now(),
                    'txn_hash' => $txHash,
                ]);

                if ($user) {
                    $user->increment('deposit_wallet', $deposit->amount);

                    Transaction::create([
                        'user_id' => $user->id,
                        'txn_number' => 'TXN-'.rand(10000000, 99999999),
                        'wallet_type' => 'deposit_wallet',
                        'amount' => $deposit->amount,
                        'charge' => 0.00,
                        'post_balance' => $user->fresh()->deposit_wallet,
                        'trx_type' => '+',
                        'type' => 'deposit',
                        'description' => 'USDT Deposit credited via iPaymentWallet (Ref: '.$deposit->deposit_ref.')',
                        'reference_id' => $deposit->id,
                        'status' => 'completed',
                    ]);
                }
            });

            return true;
        }

        return false;
    }
}
