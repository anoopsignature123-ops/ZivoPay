<?php

namespace App\Services;

use App\Models\Deposit;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DepositService
{
    /**
     * Create a new deposit request with instant direct credit to user's Fund Wallet.
     */
    public function createDeposit(User $user, float $amount, string $paymentMethod, ?string $trxHash = null, ?string $proofPath = null): Deposit
    {
        $depositRef = 'DEP-'.strtoupper(Str::random(10));
        $charge = 0.00;
        $finalAmount = $amount - $charge;

        return DB::transaction(function () use ($user, $depositRef, $amount, $charge, $finalAmount, $paymentMethod, $trxHash, $proofPath) {
            // 1. Create Deposit Record with Approved status
            $deposit = Deposit::create([
                'user_id' => $user->id,
                'deposit_ref' => $depositRef,
                'amount' => $amount,
                'charge' => $charge,
                'final_amount' => $finalAmount,
                'payment_method' => $paymentMethod,
                'trx_hash' => $trxHash,
                'proof_file' => $proofPath,
                'gateway_reference' => 'GATEWAY-'.strtoupper(Str::random(12)),
                'status' => 'approved',
                'admin_remark' => 'Instant Automated Credit to Fund Wallet.',
            ]);

            // 2. Instantly Credit User's Fund Wallet
            $user->increment('deposit_wallet', $finalAmount);

            // 3. Log Transaction Record
            Transaction::create([
                'user_id' => $user->id,
                'amount' => $finalAmount,
                'wallet_type' => 'deposit_wallet',
                'type' => 'deposit',
                'description' => "Instant Direct Fund Deposit via {$paymentMethod} (Ref: {$depositRef}, UTR/Hash: {$trxHash})",
                'trx_id' => $depositRef,
            ]);

            return $deposit;
        });
    }

    /**
     * Approve pending deposit and credit user's Fund Wallet (Admin manual action if any).
     */
    public function approveDeposit(Deposit $deposit, ?string $adminRemark = null): bool
    {
        if ($deposit->status !== 'pending') {
            return false;
        }

        return DB::transaction(function () use ($deposit, $adminRemark) {
            $deposit->update([
                'status' => 'approved',
                'admin_remark' => $adminRemark ?? 'Approved by Admin & credited to Fund Wallet.',
            ]);

            // Credit Fund Wallet
            $user = $deposit->user;
            $user->increment('deposit_wallet', $deposit->final_amount);

            // Log Transaction
            Transaction::create([
                'user_id' => $user->id,
                'amount' => $deposit->final_amount,
                'wallet_type' => 'deposit_wallet',
                'type' => 'deposit',
                'description' => "Direct Fund Deposit via {$deposit->payment_method} (Ref: {$deposit->deposit_ref}, UTR/Hash: {$deposit->trx_hash})",
                'trx_id' => $deposit->deposit_ref,
            ]);

            return true;
        });
    }

    /**
     * Reject pending deposit.
     */
    public function rejectDeposit(Deposit $deposit, ?string $adminRemark = null): bool
    {
        if ($deposit->status !== 'pending') {
            return false;
        }

        $deposit->update([
            'status' => 'rejected',
            'admin_remark' => $adminRemark ?? 'Deposit request rejected by Admin.',
        ]);

        return true;
    }
}
