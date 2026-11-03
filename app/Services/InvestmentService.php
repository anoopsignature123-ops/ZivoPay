<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserInvestment;

class InvestmentService
{
    public function calculateRoiPercentage(float $amount): float
    {
        if ($amount >= 1000000) {
            return 0.30;
        }

        if ($amount >= 500000) {
            return 0.25;
        }

        if ($amount >= 100000) {
            return 0.20;
        }

        return 0.15;
    }

    public function createInvestment(User $user, float $amount): UserInvestment
    {
        return app(MLMIncomeService::class)->createInvestment($user, $amount, 'deposit_wallet');
    }
}
