<?php

namespace App\Services\Incomes;

use App\Models\Transaction;
use App\Models\UserPackage;
use App\Services\IncomeCapService;
use Illuminate\Support\Facades\DB;

/**
 * Class RoiIncomeService
 *
 * INCOME RULE 1: DAILY ROI INCOME (Dex Trade PDF Slide 10 & 17)
 * -------------------------------------------------------------------------
 * Description:
 * Every active package investor receives 0.5% daily Return on Investment (ROI) yield
 * for a period of 400 days, subject to the 2X Non-Working Income Cap limit.
 */
class RoiIncomeService
{
    public function __construct(
        protected IncomeCapService $capService
    ) {}

    /**
     * Process Daily ROI Payout for a single active UserPackage contract.
     */
    public function processSinglePackageRoi(UserPackage $userPkg): float
    {
        if ($userPkg->status !== 'active' || ! $userPkg->user) {
            return 0.00;
        }

        $creditedAmount = 0.00;

        DB::transaction(function () use ($userPkg, &$creditedAmount) {
            $user = $userPkg->user;
            $rawDailyYield = ($userPkg->invested_amount * 0.50) / 100;

            // Apply 2X Non-Working Income Cap
            $cappedYield = $this->capService->checkAndCapNonWorking($user, $rawDailyYield);

            // Also check contract total return cap
            $remainingContractCap = (float) $userPkg->total_return_amount - (float) $userPkg->paid_roi_amount;
            $finalYield = min($cappedYield, $remainingContractCap);

            if ($finalYield <= 0) {
                $userPkg->update(['status' => 'completed']);

                return;
            }

            // 1. Credit Earning Wallet
            $user->increment('earning_wallet', $finalYield);

            // 2. Update Contract Paid ROI and Status
            $newPaidRoi = (float) $userPkg->paid_roi_amount + $finalYield;
            $newStatus = ($newPaidRoi >= (float) $userPkg->total_return_amount) ? 'completed' : 'active';

            $userPkg->update([
                'paid_roi_amount' => $newPaidRoi,
                'status' => $newStatus,
            ]);

            // 3. Log Audit Transaction
            Transaction::create([
                'user_id' => $user->id,
                'txn_number' => 'TXN-'.rand(10000000, 99999999),
                'wallet_type' => 'earning_wallet',
                'amount' => $finalYield,
                'charge' => 0.00,
                'post_balance' => $user->fresh()->earning_wallet,
                'trx_type' => '+',
                'type' => 'daily_roi',
                'description' => 'Daily ROI Yield of 0.5% ($'.number_format($finalYield, 2).") credited from investment contract (\${$userPkg->invested_amount})",
                'reference_id' => $userPkg->id,
                'status' => 'completed',
            ]);

            $creditedAmount = $finalYield;
        });

        return $creditedAmount;
    }

    /**
     * Process Daily ROI Payouts for all active contracts in system.
     */
    public function processAllDailyRoi(): array
    {
        $activePackages = UserPackage::with(['user', 'package'])
            ->where('status', 'active')
            ->whereColumn('paid_roi_amount', '<', 'total_return_amount')
            ->get();

        $processedCount = 0;
        $totalAmountCredited = 0.00;

        foreach ($activePackages as $pkg) {
            $amount = $this->processSinglePackageRoi($pkg);
            if ($amount > 0) {
                $processedCount++;
                $totalAmountCredited += $amount;
            }
        }

        return [
            'processed_contracts' => $processedCount,
            'total_roi_amount' => $totalAmountCredited,
        ];
    }
}
