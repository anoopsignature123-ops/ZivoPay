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
        $totalReturn = $amount + ($dailyProfit * 730); // Principal Return + 730 Days ROI Profit

        return DB::transaction(function () use ($user, $amount, $roiRate, $dailyProfit, $totalReturn) {
            // Deduct investment capital from user Fund Wallet
            $user->deposit_wallet -= $amount;
            $user->save();

            // Create Investment record
            $investment = UserInvestment::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'daily_roi_rate' => $roiRate,
                'daily_profit' => $dailyProfit,
                'duration_days' => 730,
                'days_received' => 0,
                'total_earned' => 0.00,
                'total_expected' => $totalReturn,
                'status' => 'active',
                'invested_at' => now(),
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

            // Distribute 15-Level Direct Business Bonus (12% total: 5% L1, 0.5% L2-L15)
            $this->distributeDirectBusinessBonus($user, $amount);

            return $investment;
        });
    }

    private function distributeDirectBusinessBonus(User $user, float $investmentAmount): void
    {
        $currentSponsorCode = $user->sponsor_code;
        $level = 1;

        while (! empty($currentSponsorCode) && $level <= 15) {
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

            // Level 1 gets 5%, Level 2-15 gets 0.50%
            $percentage = ($level === 1) ? 5.00 : 0.50;
            $commission = ($investmentAmount * $percentage) / 100;

            if ($commission > 0) {
                $sponsor->earning_wallet += $commission;
                $sponsor->save();

                Transaction::create([
                    'user_id' => $sponsor->id,
                    'from_user_id' => $user->id,
                    'trx_id' => 'DIRBUS-L'.$level.'-'.strtoupper(Str::random(8)),
                    'type' => 'direct_bonus',
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
