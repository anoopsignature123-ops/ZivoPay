<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\User;
use App\Models\UserInvestment;
use App\Models\UserReward;
use App\Models\WalletTransfer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

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
        } elseif ($amount >= 500000) {
            $percentage = 0.25;
        } elseif ($amount >= 100000) {
            $percentage = 0.20;
        } else {
            $percentage = 0.15;
        }

        $tierName = 'Capital Investment';

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
        return DB::transaction(function () use ($user, $amount, $walletType): UserInvestment {
            $user = User::query()->lockForUpdate()->findOrFail($user->id);
            if (! in_array($walletType, ['deposit_wallet', 'earning_wallet'], true)) {
                throw new \InvalidArgumentException('Unsupported investment wallet.');
            }
            if ((float) $user->{$walletType} < $amount) {
                throw ValidationException::withMessages([
                    'amount' => 'Insufficient Fund Wallet balance. Available ₹'.number_format((float) $user->{$walletType}, 2).'.',
                ]);
            }

            $monthlyAmount = round($amount * 0.08, 2);

            $investment = UserInvestment::create([
                'user_id' => $user->id,
                'plan_name' => 'Fixed Package ROI',
                'amount' => $amount,
                'daily_percentage' => 8.00,
                'roi_frequency' => 'monthly',
                'daily_amount' => $monthlyAmount,
                'monthly_amount' => $monthlyAmount,
                'total_returned' => 0.00,
                'days_completed' => 0,
                'total_days' => 25,
                'status' => 'active',
                'activated_at' => now(),
            ]);

            $user->decrement($walletType, $amount);
            $user->refresh();

            Transaction::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'wallet_type' => $walletType,
                'trx_type' => '-',
                'post_balance' => $user->{$walletType},
                'type' => 'investment',
                'description' => 'Fixed Package investment: 8% monthly ROI for 25 months on ₹'.number_format($amount, 2),
                'trx_id' => 'INV-'.strtoupper(Str::random(10)),
            ]);

            $this->evaluateRewardsUpward($user);

            return $investment;
        });
    }

    /**
     * Distribute Daily ROI & 30-Level ROI Matching Income.
     * Rules:
     * - Daily ROI to investor
     * - Level 1 Sponsor: 15% of downline's daily ROI
     * - Level 2 Sponsor: 8% of downline's daily ROI
     * - Level 3-8 Sponsor: 2% of downline's daily ROI per level
     * - Level 9-16 Sponsor: 1% of downline's daily ROI per level
     * - Level 17-30 Sponsor: 0.5% of downline's daily ROI per level
     */
    public function distributeDailyROI(UserInvestment $investment): void
    {
        app(RoiIncomeService::class)->processDailyRoiPayouts();
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
