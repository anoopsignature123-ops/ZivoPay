<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SubscriptionService
{
    public const ACTIVATION_FEE = 3000.00;

    public function activateSubscription(User $user): bool
    {
        if ($user->is_subscription_active) {
            return false;
        }

        if ($user->deposit_wallet < self::ACTIVATION_FEE) {
            return false;
        }

        return DB::transaction(function () use ($user) {
            // Deduct ₹3,000 mandatory package activation fee from user's Fund Wallet
            $user->deposit_wallet -= self::ACTIVATION_FEE;
            $user->is_subscription_active = true;
            $user->status = 'active';
            $user->activated_at = now();
            $user->save();

            // Log Fund Wallet debit transaction for Zivo Family Kit ₹3,000 subscription
            Transaction::create([
                'user_id' => $user->id,
                'trx_id' => 'SUB-'.strtoupper(Str::random(10)),
                'type' => 'subscription',
                'wallet_type' => 'fund',
                'trx_type' => '-',
                'amount' => self::ACTIVATION_FEE,
                'charge' => 0.00,
                'post_balance' => $user->deposit_wallet,
                'description' => 'Mandatory ₹3,000 Zivo Family Kit Account Activation Subscription',
            ]);

            // Distribute 15-Level Referral Incomes (5% Level 1 = ₹150, 0.50% Levels 2-15 = ₹15 per level)
            $this->distributeSubscriptionReferralIncome($user);

            return true;
        });
    }

    private function distributeSubscriptionReferralIncome(User $user): void
    {
        $currentSponsorCode = $user->sponsor_code;
        $level = 1;

        while (! empty($currentSponsorCode) && $level <= 15) {
            $sponsor = User::where('referral_code', $currentSponsorCode)->first();
            if (! $sponsor) {
                break;
            }

            // Level 1 gets 5% (₹150), Level 2-15 gets 0.50% (₹15)
            $percentage = ($level === 1) ? 5.00 : 0.50;
            $commission = (self::ACTIVATION_FEE * $percentage) / 100;

            if ($commission > 0) {
                $sponsor->earning_wallet += $commission;
                $sponsor->save();

                $typeLabel = ($level === 1) ? 'Direct Bonus Referral Income' : 'Team Level Bonus Referral Income';

                Transaction::create([
                    'user_id' => $sponsor->id,
                    'from_user_id' => $user->id,
                    'trx_id' => 'SUBINC-L'.$level.'-'.strtoupper(Str::random(8)),
                    'type' => 'subscription_level',
                    'wallet_type' => 'earning',
                    'trx_type' => '+',
                    'amount' => $commission,
                    'charge' => 0.00,
                    'post_balance' => $sponsor->earning_wallet,
                    'description' => "Level {$level} {$typeLabel} ({$percentage}%) from ₹3,000 package activation of {$user->name} ({$user->referral_code})",
                ]);
            }

            $currentSponsorCode = $sponsor->sponsor_code;
            $level++;
        }
    }
}
