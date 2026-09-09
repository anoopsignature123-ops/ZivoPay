<?php

namespace App\Services\Incomes;

use App\Models\Transaction;
use App\Models\User;
use App\Models\UserMatchingRoiContract;
use App\Services\IncomeCapService;
use Illuminate\Support\Facades\DB;

/**
 * Class MatchingRoiIncomeService
 *
 * INCOME RULE 5: MATCHING ROI INCOME (Dex Trade PDF Slide 14 & 17)
 * -------------------------------------------------------------------------
 * Description:
 * User receives 0.5% of their daily matching bonus payout every day for a period
 * of 150 days. Tracked via UserMatchingRoiContract. Subject to 8X Working Cap limit.
 */
class MatchingRoiIncomeService
{
    public function __construct(
        protected IncomeCapService $capService
    ) {}

    /**
     * Create a 150-day Matching ROI Contract when matching bonus is earned.
     */
    public function createContract(User $user, float $matchingAmount): ?UserMatchingRoiContract
    {
        if ($matchingAmount <= 0) {
            return null;
        }

        $dailyAmount = ($matchingAmount * 0.50) / 100;

        return UserMatchingRoiContract::create([
            'user_id' => $user->id,
            'matching_amount' => $matchingAmount,
            'daily_amount' => $dailyAmount,
            'duration_days' => 150,
            'days_paid' => 0,
            'total_paid' => 0.00,
            'status' => 'active',
        ]);
    }

    /**
     * Process Daily Payout for a single Matching ROI Contract.
     */
    public function processSingleContract(UserMatchingRoiContract $contract): float
    {
        if ($contract->status !== 'active' || $contract->days_paid >= $contract->duration_days || ! $contract->user) {
            return 0.00;
        }

        $creditedAmount = 0.00;

        DB::transaction(function () use ($contract, &$creditedAmount) {
            $user = $contract->user;
            $rawDaily = (float) $contract->daily_amount;

            $finalYield = $this->capService->checkAndCapWorking($user, $rawDaily);

            if ($finalYield <= 0) {
                return;
            }

            // 1. Credit Earning Wallet
            $user->increment('earning_wallet', $finalYield);

            // 2. Update Contract
            $newDaysPaid = $contract->days_paid + 1;
            $newTotalPaid = (float) $contract->total_paid + $finalYield;
            $newStatus = ($newDaysPaid >= $contract->duration_days) ? 'completed' : 'active';

            $contract->update([
                'days_paid' => $newDaysPaid,
                'total_paid' => $newTotalPaid,
                'status' => $newStatus,
            ]);

            // 3. Create Audit Transaction
            Transaction::create([
                'user_id' => $user->id,
                'txn_number' => 'TXN-'.rand(10000000, 99999999),
                'wallet_type' => 'earning_wallet',
                'amount' => $finalYield,
                'charge' => 0.00,
                'post_balance' => $user->fresh()->earning_wallet,
                'trx_type' => '+',
                'type' => 'matching_roi',
                'description' => 'Received Daily Matching ROI Income of 0.5% ($'.number_format($finalYield, 2).") on Matching Bonus contract (\${$contract->matching_amount}) - Day {$newDaysPaid}/150",
                'reference_id' => $contract->id,
                'status' => 'completed',
            ]);

            $creditedAmount = $finalYield;
        });

        return $creditedAmount;
    }

    /**
     * Process all active Matching ROI Contracts.
     */
    public function processAllMatchingRoi(): array
    {
        $activeContracts = UserMatchingRoiContract::with('user')
            ->where('status', 'active')
            ->where('days_paid', '<', 150)
            ->get();

        $processedCount = 0;
        $totalAmountCredited = 0.00;

        foreach ($activeContracts as $contract) {
            $amount = $this->processSingleContract($contract);
            if ($amount > 0) {
                $processedCount++;
                $totalAmountCredited += $amount;
            }
        }

        return [
            'processed_contracts' => $processedCount,
            'total_matching_roi_amount' => $totalAmountCredited,
        ];
    }
}
