<?php

namespace App\Services\Incomes;

use App\Models\Transaction;
use App\Models\User;
use App\Services\IncomeCapService;
use Illuminate\Support\Facades\DB;

/**
 * Class UplineMatchingIncomeService
 *
 * INCOME RULE 6: UPLINE MATCHING INCOME (Dex Trade PDF Slide 15 & 17)
 * -------------------------------------------------------------------------
 * Description:
 * 10% of the Sponsor's Matching Income is deducted from the sponsor and distributed
 * equally among all active direct referrals of that sponsor.
 * Subject to 8X Working Income Cap limit for each recipient.
 */
class UplineMatchingIncomeService
{
    public function __construct(
        protected IncomeCapService $capService
    ) {}

    /**
     * Distribute deducted 10% sponsor matching pool equally among direct referrals.
     */
    public function distributeUplineMatchingPool(User $sponsor, float $poolAmount): float
    {
        if ($poolAmount <= 0) {
            return 0.00;
        }

        $directs = User::where('sponsor_code', $sponsor->referral_code)->where('status', 'active')->get();
        if ($directs->isEmpty()) {
            return 0.00;
        }

        $directCount = $directs->count();
        $rawPerShare = $poolAmount / $directCount;
        $totalDistributed = 0.00;

        foreach ($directs as $directUser) {
            $creditedShare = $this->capService->checkAndCapWorking($directUser, $rawPerShare);

            if ($creditedShare <= 0) {
                continue;
            }

            DB::transaction(function () use ($directUser, $sponsor, $creditedShare) {
                $directUser->increment('earning_wallet', $creditedShare);

                Transaction::create([
                    'user_id' => $directUser->id,
                    'txn_number' => 'TXN-'.rand(10000000, 99999999),
                    'wallet_type' => 'earning_wallet',
                    'amount' => $creditedShare,
                    'charge' => 0.00,
                    'post_balance' => $directUser->fresh()->earning_wallet,
                    'trx_type' => '+',
                    'type' => 'upline_matching',
                    'description' => 'Received Upline Matching Income share of $'.number_format($creditedShare, 2)." from sponsor {$sponsor->name} ({$sponsor->referral_code}) matching bonus pool",
                    'reference_id' => $sponsor->id,
                    'status' => 'completed',
                ]);
            });

            $totalDistributed += $creditedShare;
        }

        return $totalDistributed;
    }
}
