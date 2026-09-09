<?php

namespace App\Services\Incomes;

use App\Models\Transaction;
use App\Models\User;
use App\Services\IncomeCapService;
use Illuminate\Support\Facades\DB;

/**
 * Class MatchingIncomeService
 *
 * INCOME RULE 3: MATCHING INCOME (10%) (Dex Trade PDF Slide 12 & 17)
 * -------------------------------------------------------------------------
 * Description:
 * 10% Binary Matching Income on matched business volume between Left and Right legs.
 * Requirement: Must have at least 1 direct referral on Left Leg and 1 on Right Leg.
 * Note: 10% of Sponsor Matching Income is deducted and distributed as Upline Matching Income.
 * Subject to 8X Working Income Cap limit.
 */
class MatchingIncomeService
{
    public const MATCHING_PERCENTAGE = 10.0;

    public function __construct(
        protected IncomeCapService $capService
    ) {}

    /**
     * Check if user meets the 1:1 matching requirement (1 active direct on left, 1 active direct on right).
     */
    public function meetsMatchingRequirement(User $user): bool
    {
        $directs = User::where('sponsor_code', $user->referral_code)->where('status', 'active')->get();

        $leftCount = $directs->where('position', 'left')->count();
        $rightCount = $directs->where('position', 'right')->count();

        // If positions are not explicitly assigned, check leftChild and rightChild
        if ($leftCount == 0 || $rightCount == 0) {
            $hasLeft = $user->leftChild() && $user->leftChild()->status === 'active';
            $hasRight = $user->rightChild() && $user->rightChild()->status === 'active';

            return $hasLeft && $hasRight;
        }

        return $leftCount >= 1 && $rightCount >= 1;
    }

    /**
     * Process Matching Income for a user.
     */
    public function processUserMatching(User $user, float $powerLegVolume, float $weakerLegVolume): float
    {
        if ($user->status !== 'active') {
            return 0.00;
        }

        if (! $this->meetsMatchingRequirement($user)) {
            return 0.00;
        }

        $matchedVolume = min($powerLegVolume, $weakerLegVolume);

        if ($matchedVolume <= 0) {
            return 0.00;
        }

        $rawMatching = ($matchedVolume * self::MATCHING_PERCENTAGE) / 100;
        $totalMatching = $this->capService->checkAndCapWorking($user, $rawMatching);

        if ($totalMatching <= 0) {
            return 0.00;
        }

        // 10% is deducted for Upline Matching Income distribution, 90% credited to user
        $uplineShare = ($totalMatching * 10.0) / 100;
        $netMatchingToUser = $totalMatching - $uplineShare;

        DB::transaction(function () use ($user, $netMatchingToUser, $matchedVolume) {
            $user->increment('earning_wallet', $netMatchingToUser);

            Transaction::create([
                'user_id' => $user->id,
                'txn_number' => 'TXN-'.rand(10000000, 99999999),
                'wallet_type' => 'earning_wallet',
                'amount' => $netMatchingToUser,
                'charge' => 0.00,
                'post_balance' => $user->fresh()->earning_wallet,
                'trx_type' => '+',
                'type' => 'matching_income',
                'description' => 'Received 10% Matching Income (Net $'.number_format($netMatchingToUser, 2).' after 10% Upline pool deduction) on matched volume of $'.number_format($matchedVolume, 2),
                'status' => 'completed',
            ]);
        });

        // Trigger Upline Matching Income Pool Distribution to Direct Referrals
        if ($uplineShare > 0) {
            app(UplineMatchingIncomeService::class)->distributeUplineMatchingPool($user, $uplineShare);
        }

        // Trigger Matching ROI Contract (0.5% daily for 150 days)
        app(MatchingRoiIncomeService::class)->createContract($user, $totalMatching);

        return $netMatchingToUser;
    }
}
