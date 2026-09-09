<?php

namespace App\Services\Incomes;

use App\Models\Transaction;
use App\Models\User;
use App\Models\UserPackage;
use App\Services\IncomeCapService;
use Illuminate\Support\Facades\DB;

/**
 * Class ReferralRoiIncomeService
 *
 * INCOME RULE 4: REFERRAL ROI INCOME (Dex Trade PDF Slide 13 & 17)
 * -------------------------------------------------------------------------
 * Description:
 * Sponsors receive 0.5% daily of the total package investment amount from all
 * their direct active members daily for up to 150 days per investment contract.
 * Subject to 8X Working Income Cap limit.
 */
class ReferralRoiIncomeService
{
    public function __construct(
        protected IncomeCapService $capService
    ) {}

    /**
     * Process Referral ROI for a sponsor across all active direct members.
     */
    public function processUserReferralRoi(User $sponsor): float
    {
        if ($sponsor->status !== 'active') {
            return 0.00;
        }

        $directs = User::where('sponsor_code', $sponsor->referral_code)->get();
        if ($directs->isEmpty()) {
            return 0.00;
        }

        $directUserIds = $directs->pluck('id')->toArray();

        // Active packages of direct members within 150 days of purchase
        $activeDirectPackages = UserPackage::whereIn('user_id', $directUserIds)
            ->where('status', 'active')
            ->where('purchased_at', '>=', now()->subDays(150))
            ->get();

        if ($activeDirectPackages->isEmpty()) {
            return 0.00;
        }

        $totalDirectInvestment = (float) $activeDirectPackages->sum('invested_amount');
        $rawDailyIncome = ($totalDirectInvestment * 0.50) / 100;

        $creditedIncome = $this->capService->checkAndCapWorking($sponsor, $rawDailyIncome);

        if ($creditedIncome <= 0) {
            return 0.00;
        }

        DB::transaction(function () use ($sponsor, $creditedIncome, $totalDirectInvestment) {
            $sponsor->increment('earning_wallet', $creditedIncome);

            Transaction::create([
                'user_id' => $sponsor->id,
                'txn_number' => 'TXN-'.rand(10000000, 99999999),
                'wallet_type' => 'earning_wallet',
                'amount' => $creditedIncome,
                'charge' => 0.00,
                'post_balance' => $sponsor->fresh()->earning_wallet,
                'trx_type' => '+',
                'type' => 'referral_roi',
                'description' => 'Received Referral ROI Income of 0.5% ($'.number_format($creditedIncome, 2).') from direct members total investment volume of $'.number_format($totalDirectInvestment, 2),
                'status' => 'completed',
            ]);
        });

        return $creditedIncome;
    }

    /**
     * Process Referral ROI for all active sponsors in the system.
     */
    public function processAllReferralRoi(): array
    {
        $sponsors = User::where('status', 'active')->has('directMembers')->get();
        $processedCount = 0;
        $totalCredited = 0.00;

        foreach ($sponsors as $sponsor) {
            $amount = $this->processUserReferralRoi($sponsor);
            if ($amount > 0) {
                $processedCount++;
                $totalCredited += $amount;
            }
        }

        return [
            'sponsors_processed' => $processedCount,
            'total_referral_roi_amount' => $totalCredited,
        ];
    }
}
