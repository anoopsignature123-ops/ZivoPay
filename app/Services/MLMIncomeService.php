<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\User;
use App\Models\UserInvestment;
use App\Models\UserReward;
use App\Models\WalletTransfer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MLMIncomeService
{
    /**
     * Activate mandatory ₹3,000 user subscription from Fund Wallet (deposit_wallet).
     */
    public function activateSubscription(User $user): bool
    {
        $service = app(SubscriptionService::class);

        return $service->activateSubscription($user);
    }

    /**
     * Distribute 15-level referral income on ₹300 subscription fee.
     */
    public function distributeSubscriptionLevelIncome(User $user, float $amount): void
    {
        $currentSponsorCode = $user->sponsor_code;
        $levelPercentages = [
            1 => 5.00,
            2 => 0.50, 3 => 0.50, 4 => 0.50, 5 => 0.50, 6 => 0.50,
            7 => 0.50, 8 => 0.50, 9 => 0.50, 10 => 0.50, 11 => 0.50,
            12 => 0.50, 13 => 0.50, 14 => 0.50, 15 => 0.50,
        ];

        for ($level = 1; $level <= 15; $level++) {
            if (! $currentSponsorCode) {
                break;
            }

            $sponsor = User::where('referral_code', $currentSponsorCode)->first();
            if (! $sponsor) {
                break;
            }

            // Only distribute subscription level income if sponsor account is active
            if (! $sponsor->isActiveForIncome()) {
                $currentSponsorCode = $sponsor->sponsor_code;

                continue;
            }

            $percentage = $levelPercentages[$level];
            $incomeAmount = round($amount * ($percentage / 100), 2);

            if ($incomeAmount > 0) {
                $sponsor->increment('earning_wallet', $incomeAmount);

                Transaction::create([
                    'user_id' => $sponsor->id,
                    'from_user_id' => $user->id,
                    'amount' => $incomeAmount,
                    'wallet_type' => 'earning_wallet',
                    'type' => 'level_income',
                    'level' => $level,
                    'description' => "Level {$level} Subscription Income ({$percentage}%) from {$user->name} ({$user->referral_code})",
                    'trx_id' => 'SUB-L'.$level.'-'.strtoupper(Str::random(8)),
                ]);
            }

            $currentSponsorCode = $sponsor->sponsor_code;
        }
    }

    /**
     * Transfer funds from Earning Wallet -> Fund Wallet (deposit_wallet).
     */
    public function transferEarningToFundWallet(User $user, float $amount): WalletTransfer
    {
        return DB::transaction(function () use ($user, $amount) {
            $user->decrement('earning_wallet', $amount);
            $user->increment('deposit_wallet', $amount);

            $trxId = 'TRF-'.strtoupper(Str::random(10));

            $transfer = WalletTransfer::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'from_wallet' => 'earning_wallet',
                'to_wallet' => 'deposit_wallet',
                'trx_id' => $trxId,
            ]);

            Transaction::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'wallet_type' => 'earning_wallet',
                'type' => 'admin_debit',
                'description' => 'Internal Wallet Transfer: Earning Wallet -> Fund Wallet',
                'trx_id' => $trxId,
            ]);

            return $transfer;
        });
    }

    /**
     * Determine tier daily percentage & monthly returns based on investment amount.
     * Poster 1 & 4 Rules:
     * - ₹1,000 to ₹99,999: 0.15% daily
     * - ₹1,00,000 to ₹4,99,999: 0.20% daily (₹5,000/mo for ₹1 Lakh)
     * - ₹5,00,000 to ₹9,99,999: 0.25% daily
     * - ₹10,00,000 & Above: 0.30% daily
     */
    public static function getTierInfo(float $amount): array
    {
        if ($amount >= 1000000) {
            $percentage = 0.30;
            $tierName = 'Diamond Tier (0.30% Daily)';
        } elseif ($amount >= 500000) {
            $percentage = 0.25;
            $tierName = 'Platinum Tier (0.25% Daily)';
        } elseif ($amount >= 100000) {
            $percentage = 0.20;
            $tierName = 'Gold Tier (0.20% Daily)';
        } else {
            $percentage = 0.15;
            $tierName = 'Silver Tier (0.15% Daily)';
        }

        $dailyAmount = round($amount * ($percentage / 100), 2);
        $monthlyAmount = round($dailyAmount * 30, 2);

        return [
            'tier_name' => $tierName,
            'daily_percentage' => $percentage,
            'daily_amount' => $dailyAmount,
            'monthly_amount' => $monthlyAmount,
        ];
    }

    /**
     * Create and activate a new investment for a user.
     */
    public function createInvestment(User $user, float $amount, string $walletType = 'deposit_wallet'): UserInvestment
    {
        return DB::transaction(function () use ($user, $amount, $walletType) {
            $tierInfo = self::getTierInfo($amount);

            // Create investment record
            $investment = UserInvestment::create([
                'user_id' => $user->id,
                'plan_name' => $tierInfo['tier_name'],
                'amount' => $amount,
                'daily_percentage' => $tierInfo['daily_percentage'],
                'daily_amount' => $tierInfo['daily_amount'],
                'monthly_amount' => $tierInfo['monthly_amount'],
                'total_returned' => 0.00,
                'days_completed' => 0,
                'total_days' => 730, // 24 Months
                'status' => 'active',
                'activated_at' => now(),
            ]);

            // Deduct wallet if paid via deposit wallet
            if ($walletType === 'deposit_wallet') {
                $user->decrement('deposit_wallet', $amount);
            }

            // Set user active if inactive
            if ($user->status !== 'active') {
                $user->update([
                    'status' => 'active',
                    'activated_at' => now(),
                ]);
            }

            // Log investment transaction
            Transaction::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'wallet_type' => $walletType,
                'type' => 'investment',
                'description' => "Activated {$tierInfo['tier_name']} for ₹".number_format($amount, 2),
                'trx_id' => 'INV-'.strtoupper(Str::random(10)),
            ]);

            // Distribute 15-Level Direct Income (12% Total)
            $this->distributeDirectLevelIncome($investment);

            // Check rewards up the upline tree
            $this->evaluateRewardsUpward($user);

            return $investment;
        });
    }

    /**
     * Distribute 15-Level Direct Income on investment activation.
     * Poster 2 Rules:
     * Level 1: 5.00%
     * Level 2 to 15: 0.50% per level (Total 12%)
     */
    public function distributeDirectLevelIncome(UserInvestment $investment): void
    {
        $investor = $investment->user;
        $currentSponsorCode = $investor->sponsor_code;
        $amount = $investment->amount;

        // Level percentages array (Index 1 to 15)
        $levelPercentages = [
            1 => 5.00,
            2 => 0.50,
            3 => 0.50,
            4 => 0.50,
            5 => 0.50,
            6 => 0.50,
            7 => 0.50,
            8 => 0.50,
            9 => 0.50,
            10 => 0.50,
            11 => 0.50,
            12 => 0.50,
            13 => 0.50,
            14 => 0.50,
            15 => 0.50,
        ];

        for ($level = 1; $level <= 15; $level++) {
            if (! $currentSponsorCode) {
                break;
            }

            $sponsor = User::where('referral_code', $currentSponsorCode)->first();
            if (! $sponsor) {
                break;
            }

            // Only distribute direct level income if sponsor account is active
            if (! $sponsor->isActiveForIncome()) {
                $currentSponsorCode = $sponsor->sponsor_code;

                continue;
            }

            $percentage = $levelPercentages[$level];
            $incomeAmount = round($amount * ($percentage / 100), 2);

            if ($incomeAmount > 0) {
                $sponsor->increment('earning_wallet', $incomeAmount);

                Transaction::create([
                    'user_id' => $sponsor->id,
                    'from_user_id' => $investor->id,
                    'amount' => $incomeAmount,
                    'wallet_type' => 'earning_wallet',
                    'type' => 'level_income',
                    'level' => $level,
                    'description' => "Level {$level} Direct Income ({$percentage}%) from {$investor->name} ({$investor->referral_code})",
                    'trx_id' => 'INC-L'.$level.'-'.strtoupper(Str::random(8)),
                ]);
            }

            $currentSponsorCode = $sponsor->sponsor_code;
        }
    }

    /**
     * Distribute Daily ROI & 15-Level ROI Matching Income (26% Total).
     * Poster 2 Rules:
     * - Daily ROI to investor
     * - Level 1 Sponsor: 10% of downline's daily ROI
     * - Level 2 Sponsor: 3% of downline's daily ROI
     * - Level 3 to 15 Sponsor: 1% of downline's daily ROI per level
     */
    public function distributeDailyROI(UserInvestment $investment): void
    {
        if ($investment->status !== 'active' || $investment->days_completed >= $investment->total_days) {
            return;
        }

        DB::transaction(function () use ($investment) {
            $investor = $investment->user;
            $dailyRoiAmount = $investment->daily_amount;

            // 1. Credit Investor's Earning Wallet
            $investor->increment('earning_wallet', $dailyRoiAmount);
            $investment->increment('total_returned', $dailyRoiAmount);
            $investment->increment('days_completed');
            $investment->update(['last_payout_at' => now()]);

            Transaction::create([
                'user_id' => $investor->id,
                'amount' => $dailyRoiAmount,
                'wallet_type' => 'earning_wallet',
                'type' => 'daily_roi',
                'description' => "Daily ROI ({$investment->daily_percentage}%) for {$investment->plan_name}",
                'trx_id' => 'ROI-'.strtoupper(Str::random(10)),
            ]);

            // Check completion
            if ($investment->days_completed >= $investment->total_days) {
                $investment->update(['status' => 'completed']);
            }

            // 2. Distribute 15-Level Secondary ROI Level Income (26% Total)
            $roiLevelPercentages = [
                1 => 10.00,
                2 => 3.00,
                3 => 1.00,
                4 => 1.00,
                5 => 1.00,
                6 => 1.00,
                7 => 1.00,
                8 => 1.00,
                9 => 1.00,
                10 => 1.00,
                11 => 1.00,
                12 => 1.00,
                13 => 1.00,
                14 => 1.00,
                15 => 1.00,
            ];

            $currentSponsorCode = $investor->sponsor_code;

            for ($level = 1; $level <= 15; $level++) {
                if (! $currentSponsorCode) {
                    break;
                }

                $sponsor = User::where('referral_code', $currentSponsorCode)->first();
                if (! $sponsor) {
                    break;
                }

                // Only distribute ROI matching level income if sponsor account is active
                if (! $sponsor->isActiveForIncome()) {
                    $currentSponsorCode = $sponsor->sponsor_code;

                    continue;
                }

                $percentage = $roiLevelPercentages[$level];
                $roiMatchingIncome = round($dailyRoiAmount * ($percentage / 100), 2);

                if ($roiMatchingIncome > 0) {
                    $sponsor->increment('earning_wallet', $roiMatchingIncome);

                    Transaction::create([
                        'user_id' => $sponsor->id,
                        'from_user_id' => $investor->id,
                        'amount' => $roiMatchingIncome,
                        'wallet_type' => 'earning_wallet',
                        'type' => 'roi_level_income',
                        'level' => $level,
                        'description' => "Level {$level} ROI Matching Income ({$percentage}%) from {$investor->name}'s Daily ROI",
                        'trx_id' => 'ROIL-L'.$level.'-'.strtoupper(Str::random(8)),
                    ]);
                }

                $currentSponsorCode = $sponsor->sponsor_code;
            }
        });
    }

    /**
     * Check and award Business Rewards to user.
     * Poster 3 Rules:
     * - ₹5 Lakh: Mobile
     * - ₹10 Lakh: Laptop
     * - ₹25 Lakh: EV Scooty
     * - ₹50 Lakh / ₹1 Crore: Car DP 3 Lakh
     * - ₹5 Crore: Tata Punch
     * - ₹10 Crore: Tata Sierra
     */
    public static function getRewardTiers(): array
    {
        return [
            ['title' => 'Executive Rank', 'business' => 500000, 'item' => 'Mobile Phone (स्मार्टफोन)'],
            ['title' => 'Senior Executive', 'business' => 1000000, 'item' => 'Laptop (लैपटॉप)'],
            ['title' => 'Manager Rank', 'business' => 2500000, 'item' => 'EV Scooty (इलेक्ट्रिक स्कूटी)'],
            ['title' => 'Senior Manager', 'business' => 5000000, 'item' => 'Car DP ₹3 Lakh (कार/बाइक)'],
            ['title' => 'Director Rank', 'business' => 10000000, 'item' => 'Car DP ₹3 Lakh (कार/बाइक)'],
            ['title' => 'Crown Director', 'business' => 50000000, 'item' => 'Tata Punch (टाटा पंच)'],
            ['title' => 'Royal Crown Director', 'business' => 100000000, 'item' => 'Tata Sierra (टाटा सेरा SUV)'],
        ];
    }

    /**
     * Evaluate reward achievements up the sponsor chain.
     */
    public function evaluateRewardsUpward(User $user): void
    {
        $current = $user;

        while ($current && $current->sponsor_code) {
            $sponsor = User::where('referral_code', $current->sponsor_code)->first();
            if (! $sponsor) {
                break;
            }

            $this->checkUserRewardAchievements($sponsor);
            $current = $sponsor;
        }
    }

    /**
     * Evaluate team business for a specific user and award eligible rewards.
     */
    public function checkUserRewardAchievements(User $user): void
    {
        // Only active users can achieve milestone rewards
        if (! $user->isActiveForIncome()) {
            return;
        }

        // Calculate total downline business
        $downlineIds = $user->getBranchUserIds();
        $totalTeamBusiness = UserInvestment::whereIn('user_id', $downlineIds)->sum('amount');

        $rewardTiers = self::getRewardTiers();

        foreach ($rewardTiers as $tier) {
            if ($totalTeamBusiness >= $tier['business']) {
                $exists = UserReward::where('user_id', $user->id)
                    ->where('business_required', $tier['business'])
                    ->exists();

                if (! $exists) {
                    UserReward::create([
                        'user_id' => $user->id,
                        'reward_title' => $tier['title'],
                        'business_required' => $tier['business'],
                        'reward_item' => $tier['item'],
                        'type' => 'team_business',
                        'status' => 'achieved',
                        'achieved_at' => now(),
                    ]);

                    Transaction::create([
                        'user_id' => $user->id,
                        'amount' => 0.00,
                        'wallet_type' => 'earning_wallet',
                        'type' => 'reward',
                        'description' => "Achieved {$tier['title']} Reward: {$tier['item']} for ₹".number_format($tier['business'], 2).' Team Business',
                        'trx_id' => 'RWD-'.strtoupper(Str::random(8)),
                    ]);
                }
            }
        }
    }
}
