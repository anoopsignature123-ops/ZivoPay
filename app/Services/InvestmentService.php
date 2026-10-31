<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\User;
use App\Models\UserInvestment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InvestmentService
{
    public function calculateRoiPercentage(float $amount): float
    {
        if ($amount >= 1000000) {
            return 0.30;
        } elseif ($amount >= 500000) {
            return 0.25;
        } elseif ($amount >= 100000) {
            return 0.20;
        } else {
            return 0.15;
        }
    }

    public function createInvestment(User $user, float $amount): UserInvestment
    {
        if ($user->deposit_wallet < $amount) {
            throw new \Exception('Insufficient Fund Wallet balance.');
        }

        $roiRate = $this->calculateRoiPercentage($amount);
        $dailyProfit = ($amount * $roiRate) / 100;
        $totalReturn = $amount + ($dailyProfit * 750); // Principal Return + 750 Days ROI Profit

        return DB::transaction(function () use ($user, $amount, $roiRate) {
            // Deduct investment capital from user Fund Wallet
            $user->deposit_wallet -= $amount;
            $user->save();

            $tierInfo = MLMIncomeService::getTierInfo($amount);

            // Create Investment record
            $investment = UserInvestment::create([
                'user_id' => $user->id,
                'plan_name' => $tierInfo['tier_name'],
                'amount' => $amount,
                'daily_percentage' => $tierInfo['daily_percentage'],
                'daily_amount' => $tierInfo['daily_amount'],
                'monthly_amount' => $tierInfo['monthly_amount'],
                'total_returned' => 0.00,
                'days_completed' => 0,
                'total_days' => 750,
                'status' => 'active',
                'activated_at' => now(),
            ]);

            // Log Fund Wallet debit transaction
            Transaction::create([
                'user_id' => $user->id,
                'trx_id' => 'INV-'.strtoupper(Str::random(10)),
                'type' => 'investment',
                'wallet_type' => 'fund',
                'trx_type' => '-',
                'amount' => $amount,
                'charge' => 0.00,
                'post_balance' => $user->deposit_wallet,
                'description' => 'Capital Investment of ₹'.number_format($amount, 2).' at '.number_format($roiRate, 2).'% Daily ROI',
            ]);

            return $investment;
        });
    }
}
