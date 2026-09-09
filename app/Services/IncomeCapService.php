<?php

namespace App\Services;

use App\Models\User;

/**
 * Class IncomeCapService
 *
 * DEX TRADE DUAL INCOME CAPPING ENFORCER (PDF Presentation Slide 17)
 * -------------------------------------------------------------------
 * Terms & Conditions:
 * 1. Working Income Limit: 8X (800% of Active Package Investment).
 * 2. Non-Working Income Limit: 2X (200% of Active Package Investment).
 */
class IncomeCapService
{
    /**
     * Evaluate and cap Non-Working (ROI) Income for a user under their 2X limit.
     */
    public function checkAndCapNonWorking(User $user, float $requestedAmount): float
    {
        if ($requestedAmount <= 0) {
            return 0.00;
        }

        $remainingCap = $user->remaining_non_working_cap;

        return min($requestedAmount, $remainingCap);
    }

    /**
     * Evaluate and cap Working Income for a user under their 8X limit.
     */
    public function checkAndCapWorking(User $user, float $requestedAmount): float
    {
        if ($requestedAmount <= 0) {
            return 0.00;
        }

        $remainingCap = $user->remaining_working_cap;

        return min($requestedAmount, $remainingCap);
    }
}
