<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserInvestment;
use App\Models\UserReward;

class RewardService
{
    public function getDirectBusinessRewardTiers(): array
    {
        return [
            ['target' => 500000, 'reward' => 'Mobile Phone', 'category' => 'Direct'],
            ['target' => 1000000, 'reward' => 'Laptop', 'category' => 'Direct'],
            ['target' => 2500000, 'reward' => 'EV Scooty', 'category' => 'Direct'],
            ['target' => 10000000, 'reward' => 'Car Downpayment ₹3 Lakh', 'category' => 'Direct'],
            ['target' => 25000000, 'reward' => 'Car Downpayment ₹3 Lakh', 'category' => 'Direct'],
            ['target' => 50000000, 'reward' => 'Tata Punch Car', 'category' => 'Direct'],
            ['target' => 50000000, 'reward' => 'Tata Sierra SUV', 'category' => 'Direct'],
        ];
    }

    public function getTeamBusinessRewardTiers(): array
    {
        return [
            ['target' => 1000000, 'reward' => 'Mobile Phone', 'category' => 'Team'],
            ['target' => 2500000, 'reward' => 'Laptop', 'category' => 'Team'],
            ['target' => 5000000, 'reward' => 'EV Scooty', 'category' => 'Team'],
            ['target' => 10000000, 'reward' => 'Car Downpayment ₹3 Lakh', 'category' => 'Team'],
            ['target' => 25000000, 'reward' => 'Car Downpayment ₹3 Lakh', 'category' => 'Team'],
            ['target' => 50000000, 'reward' => 'Tata Punch Car', 'category' => 'Team'],
            ['target' => 100000000, 'reward' => 'Tata Sierra SUV', 'category' => 'Team'],
        ];
    }

    public function getUserDirectBusiness(User $user): float
    {
        return User::where('sponsor_code', $user->referral_code)
            ->withSum('investments', 'amount')
            ->get()
            ->sum('investments_sum_amount') ?: 0.00;
    }

    public function getUserTeamBusiness(User $user): float
    {
        // Total investment volume of all downlines up to 15 levels
        $downlineUserIds = $this->getDownlineUserIds($user->referral_code);

        return UserInvestment::whereIn('user_id', $downlineUserIds)->sum('amount') ?: 0.00;
    }

    private function getDownlineUserIds(string $referralCode, int $currentLevel = 1, int $maxLevel = 15): array
    {
        if ($currentLevel > $maxLevel) {
            return [];
        }

        $directs = User::where('sponsor_code', $referralCode)->pluck('id', 'referral_code')->toArray();
        $ids = array_values($directs);

        foreach (array_keys($directs) as $code) {
            $ids = array_merge($ids, $this->getDownlineUserIds($code, $currentLevel + 1, $maxLevel));
        }

        return array_unique($ids);
    }

    public function evaluateAndAwardRewards(User $user): void
    {
        $directBusiness = $this->getUserDirectBusiness($user);
        $teamBusiness = $this->getUserTeamBusiness($user);

        // Evaluate Direct Rewards
        foreach ($this->getDirectBusinessRewardTiers() as $tier) {
            if ($directBusiness >= $tier['target']) {
                $exists = UserReward::where('user_id', $user->id)
                    ->where('target_amount', $tier['target'])
                    ->where('reward_name', $tier['reward'])
                    ->exists();

                if (! $exists) {
                    UserReward::create([
                        'user_id' => $user->id,
                        'target_amount' => $tier['target'],
                        'reward_name' => $tier['reward'].' (Direct Business)',
                        'status' => 'claimed',
                        'achieved_at' => now(),
                    ]);
                }
            }
        }

        // Evaluate Team Rewards
        foreach ($this->getTeamBusinessRewardTiers() as $tier) {
            if ($teamBusiness >= $tier['target']) {
                $exists = UserReward::where('user_id', $user->id)
                    ->where('target_amount', $tier['target'])
                    ->where('reward_name', 'like', "%{$tier['reward']}%")
                    ->exists();

                if (! $exists) {
                    UserReward::create([
                        'user_id' => $user->id,
                        'target_amount' => $tier['target'],
                        'reward_name' => $tier['reward'].' (Team Business)',
                        'status' => 'claimed',
                        'achieved_at' => now(),
                    ]);
                }
            }
        }
    }
}
