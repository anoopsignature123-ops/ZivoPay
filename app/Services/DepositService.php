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
     * Create a deposit request for admin review. Only verified gateway payments are credited automatically.
     */
    public function createDeposit(User $user, float $amount, string $paymentMethod, ?string $trxHash = null, ?string $proofPath = null): Deposit
    {
        $depositRef = 'DEP-'.strtoupper(Str::random(10));
        $charge = 0.00;
        $finalAmount = $amount - $charge;

        return Deposit::create([
            'user_id' => $user->id,
            'deposit_ref' => $depositRef,
            'amount' => $amount,
            'charge' => $charge,
            'final_amount' => $finalAmount,
            'payment_method' => $paymentMethod,
            'trx_hash' => $trxHash,
            'proof_file' => $proofPath,
            'gateway_reference' => 'GATEWAY-'.strtoupper(Str::random(12)),
            'status' => 'pending',
            'admin_remark' => 'Payment submitted; awaiting admin verification.',
        ]);
    }

    /**
     * Approve pending deposit and credit user's Fund Wallet (Admin manual action if any).
     */
    public function approveDeposit(Deposit $deposit, ?string $adminRemark = null): bool
    {
        if ($deposit->status !== 'pending' || filled($deposit->payment_provider)) {
            return false;
        }

        return DB::transaction(function () use ($deposit, $adminRemark): bool {
            $lockedDeposit = Deposit::query()->lockForUpdate()->find($deposit->id);
            if (! $lockedDeposit || $lockedDeposit->status !== 'pending' || filled($lockedDeposit->payment_provider)) {
                return false;
            }
            $lockedDeposit->update([
                'status' => 'approved',
                'admin_remark' => $adminRemark ?? 'Approved by Admin & credited to Fund Wallet.',
            ]);

            $user = $lockedDeposit->user()->lockForUpdate()->firstOrFail();
            $user->deposit_wallet = (float) $user->deposit_wallet + (float) $lockedDeposit->final_amount;
            $user->save();

            Transaction::create([
                'user_id' => $user->id,
                'amount' => $lockedDeposit->final_amount,
                'wallet_type' => 'deposit_wallet',
                'type' => 'deposit',
                'trx_type' => '+',
                'charge' => 0,
                'post_balance' => $user->deposit_wallet,
                'description' => "Direct Fund Deposit via {$lockedDeposit->payment_method} (Ref: {$lockedDeposit->deposit_ref}, UTR/Hash: {$lockedDeposit->trx_hash})",
                'trx_id' => $lockedDeposit->deposit_ref,
            ]);

            return true;
        });
    }

    /**
     * Reject pending deposit.
     */
    public function rejectDeposit(Deposit $deposit, ?string $adminRemark = null): bool
    {
        if ($deposit->status !== 'pending' || filled($deposit->payment_provider)) {
            return false;
        }

        return DB::transaction(function () use ($deposit, $adminRemark): bool {
            $lockedDeposit = Deposit::query()->lockForUpdate()->find($deposit->id);
            if (! $lockedDeposit || $lockedDeposit->status !== 'pending' || filled($lockedDeposit->payment_provider)) {
                return false;
            }
            $lockedDeposit->update([
                'status' => 'rejected',
                'admin_remark' => $adminRemark ?? 'Deposit request rejected by Admin.',
            ]);

            return true;
        });
    }
}
