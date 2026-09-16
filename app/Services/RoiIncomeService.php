<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\User;
use App\Models\UserInvestment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RoiIncomeService
{
    public function calculateDailyRoiPercentage(float $amount): float
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

    public function processDailyRoiPayouts(): int
    {
        $processedCount = 0;

        // 1. Process Daily ROI for Active Capital Investments
        $activeInvestments = UserInvestment::where('status', 'active')
            ->where('days_received', '<', 730)
            ->get();

        foreach ($activeInvestments as $investment) {
            DB::transaction(function () use ($investment, &$processedCount) {
                $user = $investment->user;
                if (! $user) {
                    return;
                }

                $dailyAmount = $investment->daily_profit;

                // Credit Daily ROI to User's Earning Wallet
                $user->earning_wallet += $dailyAmount;
                $user->save();

                $investment->days_received += 1;
                $investment->total_earned += $dailyAmount;
                if ($investment->days_received >= 730) {
                    $investment->status = 'completed';
                    $investment->completed_at = now();
                }
                $investment->save();

                // Log Daily ROI Transaction
                Transaction::create([
                    'user_id' => $user->id,
                    'trx_id' => 'DROI-'.strtoupper(Str::random(10)),
                    'type' => 'daily_roi',
                    'wallet_type' => 'earning',
                    'trx_type' => '+',
                    'amount' => $dailyAmount,
                    'charge' => 0.00,
                    'post_balance' => $user->earning_wallet,
                    'description' => "Daily ROI Income (Day {$investment->days_received}/730) on Capital ₹".number_format($investment->amount, 2),
                ]);

                // Distribute 26% 15-Level Secondary ROI Matching Bonus (L1: 10%, L2: 3%, L3-L15: 1%)
                $this->distributeRoiOnRoiMatchingBonus($user, $dailyAmount);

                $processedCount++;
            });
        }

        // 2. Process 24-Hour Compound Daily Profit on Idle Fund Wallet Balance
        // Requirement: Activation ke 24 hours ke baad Fund Wallet balance pe Daily Profit Start hoga!
        $activeUsersWithFundBalance = User::where('is_subscription_active', true)
            ->where('deposit_wallet', '>=', 1000)
            ->get();

        foreach ($activeUsersWithFundBalance as $user) {
            // Check if activated at least 24 hours ago
            if ($user->activated_at && $user->activated_at->diffInHours(now()) >= 24) {
                DB::transaction(function () use ($user, &$processedCount) {
                    $fundBalance = $user->deposit_wallet;
                    $rate = $this->calculateDailyRoiPercentage($fundBalance);
                    $dailyProfit = ($fundBalance * $rate) / 100;

                    if ($dailyProfit > 0) {
                        $user->earning_wallet += $dailyProfit;
                        $user->save();

                        Transaction::create([
                            'user_id' => $user->id,
                            'trx_id' => 'FWPROFIT-'.strtoupper(Str::random(10)),
                            'type' => 'daily_roi',
                            'wallet_type' => 'earning',
                            'trx_type' => '+',
                            'amount' => $dailyProfit,
                            'charge' => 0.00,
                            'post_balance' => $user->earning_wallet,
                            'description' => "24-Hour Fund Wallet Daily Profit ({$rate}%) on Balance ₹".number_format($fundBalance, 2),
                        ]);

                        // Distribute 26% 15-Level Secondary ROI Matching Bonus
                        $this->distributeRoiOnRoiMatchingBonus($user, $dailyProfit);

                        $processedCount++;
                    }
                });
            }
        }

        return $processedCount;
    }

    private function distributeRoiOnRoiMatchingBonus(User $user, float $dailyRoiAmount): void
    {
        $currentSponsorCode = $user->sponsor_code;
        $level = 1;

        while (! empty($currentSponsorCode) && $level <= 15) {
            $sponsor = User::where('referral_code', $currentSponsorCode)->first();
            if (! $sponsor) {
                break;
            }

            // ROI Matching Table from official plan poster:
            // Level 1: 10%, Level 2: 3%, Level 3-15: 1% each (Total 26%)
            if ($level === 1) {
                $percentage = 10.00;
            } elseif ($level === 2) {
                $percentage = 3.00;
            } else {
                $percentage = 1.00;
            }

            $commission = ($dailyRoiAmount * $percentage) / 100;

            if ($commission > 0) {
                $sponsor->earning_wallet += $commission;
                $sponsor->save();

                Transaction::create([
                    'user_id' => $sponsor->id,
                    'from_user_id' => $user->id,
                    'trx_id' => 'ROILVL-L'.$level.'-'.strtoupper(Str::random(8)),
                    'type' => 'level_roi',
                    'wallet_type' => 'earning',
                    'trx_type' => '+',
                    'amount' => $commission,
                    'charge' => 0.00,
                    'post_balance' => $sponsor->earning_wallet,
                    'description' => "Level {$level} ROI Matching Bonus ({$percentage}%) from member {$user->name}'s Daily ROI of ₹".number_format($dailyRoiAmount, 2),
                ]);
            }

            $currentSponsorCode = $sponsor->sponsor_code;
            $level++;
        }
    }
}
