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

            // Distribute 30-Level Direct Business Bonus (L1: 8%, L2: 8%, L3-L8: 2%, L9-L16: 1%, L17-L30: 0.5%)
            $this->distributeDirectBusinessBonus($user, $amount);

            return $investment;
        });
    }

    private function distributeDirectBusinessBonus(User $user, float $investmentAmount): void
    {
        $currentSponsorCode = $user->sponsor_code;
        $level = 1;

        while (! empty($currentSponsorCode) && $level <= 30) {
            $sponsor = User::where('referral_code', $currentSponsorCode)->first();
            if (! $sponsor) {
                break;
            }

            // Only distribute direct business bonus if sponsor account is active
            if (! $sponsor->isActiveForIncome()) {
                $currentSponsorCode = $sponsor->sponsor_code;
                $level++;

                continue;
            }

            // L1: 8%, L2: 8%, L3-L8: 2%, L9-L16: 1%, L17-L30: 0.5%
            if ($level === 1) {
                $percentage = 8.00;
            } elseif ($level === 2) {
                $percentage = 8.00;
            } elseif ($level <= 8) {
                $percentage = 2.00;
            } elseif ($level <= 16) {
                $percentage = 1.00;
            } else {
                $percentage = 0.50;
            }

            $commission = ($investmentAmount * $percentage) / 100;

            if ($commission > 0) {
                $sponsor->earning_wallet += $commission;
                $sponsor->save();

                Transaction::create([
                    'user_id' => $sponsor->id,
                    'from_user_id' => $user->id,
                    'trx_id' => 'INC-L'.$level.'-'.strtoupper(Str::random(8)),
                    'type' => 'level_income',
                    'wallet_type' => 'earning',
                    'trx_type' => '+',
                    'amount' => $commission,
                    'charge' => 0.00,
                    'post_balance' => $sponsor->earning_wallet,
                    'description' => "Level {$level} Direct Business Bonus ({$percentage}%) from member {$user->name}'s investment of ₹".number_format($investmentAmount, 2),
                ]);
            }

            $currentSponsorCode = $sponsor->sponsor_code;
            $level++;
        }
    }
}
