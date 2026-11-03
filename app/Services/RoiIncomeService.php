<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\User;
use App\Models\UserInvestment;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RoiIncomeService
{
    public function calculateDailyRoiPercentage(float $amount): float
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

    public function processDailyRoiPayouts(): int
    {
        $processedCount = 0;

        UserInvestment::query()
            ->where('status', 'active')
            ->whereColumn('days_completed', '<', 'total_days')
            ->orderBy('id')
            ->chunkById(100, function ($investments) use (&$processedCount): void {
                foreach ($investments as $investment) {
                    if ($this->processInvestmentRoi((int) $investment->id)) {
                        $processedCount++;
                    }
                }
            });

        User::query()
            ->where('deposit_wallet', '>=', 1000)
            ->orderBy('id')
            ->chunkById(100, function ($users) use (&$processedCount): void {
                foreach ($users as $user) {
                    if ($this->processFundWalletRoi((int) $user->id)) {
                        $processedCount++;
                    }
                }
            });

        return $processedCount;
    }

    private function processInvestmentRoi(int $investmentId): bool
    {
        return DB::transaction(function () use ($investmentId): bool {
            $investment = UserInvestment::query()->lockForUpdate()->find($investmentId);
            if (! $investment || $investment->status !== 'active' || $investment->days_completed >= $investment->total_days) {
                return false;
            }

            $user = User::query()->lockForUpdate()->find($investment->user_id);
            if (! $user) {
                return false;
            }

            $lastPayoutAt = $investment->last_payout_at;
            if (! $lastPayoutAt && $investment->days_completed > 0) {
                $lastPayoutAt = Transaction::query()
                    ->where('user_id', $user->id)
                    ->where('type', 'daily_roi')
                    ->where('trx_id', 'like', 'DROI-%')
                    ->latest('id')
                    ->value('created_at');
            }

            $lastPayoutAt ??= $investment->activated_at;
            if (! $lastPayoutAt) {
                return false;
            }
            $dueAt = $investment->roi_frequency === 'monthly'
                ? Carbon::parse($lastPayoutAt)->addMonthNoOverflow()
                : Carbon::parse($lastPayoutAt)->addDay();
            if ($dueAt->isFuture()) {
                return false;
            }

            $dailyAmount = round((float) $investment->daily_amount, 2);
            if ($dailyAmount <= 0) {
                return false;
            }

            $user->earning_wallet = round((float) $user->earning_wallet + $dailyAmount, 2);
            $user->save();

            $investment->days_completed++;
            $investment->total_returned = round((float) $investment->total_returned + $dailyAmount, 2);
            $investment->last_payout_at = now();
            if ($investment->days_completed >= $investment->total_days) {
                $investment->status = 'completed';
            }
            $investment->save();

            Transaction::create([
                'user_id' => $user->id,
                'trx_id' => 'DROI-'.strtoupper(Str::random(10)),
                'type' => $investment->roi_frequency === 'monthly' ? 'fixed_package_roi' : 'daily_roi',
                'wallet_type' => 'earning',
                'trx_type' => '+',
                'amount' => $dailyAmount,
                'charge' => 0,
                'post_balance' => $user->earning_wallet,
                'description' => $investment->roi_frequency === 'monthly'
                    ? "Fixed Package ROI (Cycle {$investment->days_completed}/{$investment->total_days}) on Capital ₹".number_format((float) $investment->amount, 2)
                    : "Daily ROI (Day {$investment->days_completed}/{$investment->total_days}) on Capital ₹".number_format((float) $investment->amount, 2),
            ]);

            $this->distributeRoiOnRoiMatchingBonus($user, $dailyAmount);

            return true;
        });
    }

    private function processFundWalletRoi(int $userId): bool
    {
        return DB::transaction(function () use ($userId): bool {
            $user = User::query()->lockForUpdate()->find($userId);
            if (! $user || (float) $user->deposit_wallet < 1000) {
                return false;
            }

            $lastFundRoi = Transaction::query()
                ->where('user_id', $user->id)
                ->where('type', 'daily_roi')
                ->where('trx_id', 'like', 'FWPROFIT-%')
                ->latest('id')
                ->value('created_at');
            $lastFundCredit = Transaction::query()
                ->where('user_id', $user->id)
                ->whereIn('wallet_type', ['fund', 'deposit', 'deposit_wallet'])
                ->where('trx_type', '+')
                ->latest('id')
                ->value('created_at');
            $cycleAnchor = $lastFundRoi ?? $lastFundCredit ?? $user->created_at;

            if (! $cycleAnchor || Carbon::parse($cycleAnchor)->gt(now()->subDay())) {
                return false;
            }

            $fundBalance = (float) $user->deposit_wallet;
            $rate = $this->calculateDailyRoiPercentage($fundBalance);
            $dailyProfit = round(($fundBalance * $rate) / 100, 2);
            if ($dailyProfit <= 0) {
                return false;
            }

            $user->earning_wallet = round((float) $user->earning_wallet + $dailyProfit, 2);
            $user->save();

            Transaction::create([
                'user_id' => $user->id,
                'trx_id' => 'FWPROFIT-'.strtoupper(Str::random(10)),
                'type' => 'daily_roi',
                'wallet_type' => 'earning',
                'trx_type' => '+',
                'amount' => $dailyProfit,
                'charge' => 0,
                'post_balance' => $user->earning_wallet,
                'description' => "Fund Wallet Daily ROI ({$rate}%) on Balance ₹".number_format($fundBalance, 2),
            ]);

            $this->distributeRoiOnRoiMatchingBonus($user, $dailyProfit);

            return true;
        });
    }

    private function distributeRoiOnRoiMatchingBonus(User $user, float $dailyRoiAmount): void
    {
        $currentSponsorCode = $user->sponsor_code;

        for ($level = 1; $level <= 30 && ! empty($currentSponsorCode); $level++) {
            $sponsor = User::query()->where('referral_code', $currentSponsorCode)->lockForUpdate()->first();
            if (! $sponsor) {
                break;
            }

            $activatedAt = $sponsor->subscription_activated_at ?? $sponsor->activated_at;
            if ($sponsor->isActiveForIncome() && $activatedAt && Carbon::parse($activatedAt)->lte(now()->subDay())) {
                $percentage = $this->roiMatchingPercentage($level);
                $commission = round($dailyRoiAmount * $percentage / 100, 2);

                if ($commission > 0) {
                    $sponsor->earning_wallet = round((float) $sponsor->earning_wallet + $commission, 2);
                    $sponsor->save();

                    Transaction::create([
                        'user_id' => $sponsor->id,
                        'from_user_id' => $user->id,
                        'trx_id' => 'ROILVL-L'.$level.'-'.strtoupper(Str::random(8)),
                        'type' => 'roi_level_income',
                        'wallet_type' => 'earning',
                        'trx_type' => '+',
                        'level' => $level,
                        'amount' => $commission,
                        'charge' => 0,
                        'post_balance' => $sponsor->earning_wallet,
                        'description' => "Level {$level} ROI-on-ROI commission ({$percentage}%) from {$user->name}'s ROI of ₹".number_format($dailyRoiAmount, 2),
                    ]);
                }
            }

            $currentSponsorCode = $sponsor->sponsor_code;
        }
    }

    private function roiMatchingPercentage(int $level): float
    {
        if ($level === 1) {
            return 15.0;
        }

        if ($level === 2) {
            return 8.0;
        }

        if ($level <= 8) {
            return 2.0;
        }

        if ($level <= 16) {
            return 1.0;
        }

        return 0.5;
    }
}
